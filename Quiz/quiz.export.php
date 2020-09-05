<?php
  if (isset($_GET['uqid'])) {
    require_once "../header.php";
    require_once "../includes/db.inc.php";
    // Get the entire quiz details
    $selectQuizAttributes = $conn->prepare("SELECT questions.question_number, questions.description AS question_desc, questions.type,
                                                     options.option_number, options.description AS option_desc, options.isanswer, options.weightage
                                              FROM questions INNER JOIN options
                                              WHERE questions.qnid = options.qnid AND questions.qid IN
                                                (SELECT qid FROM quizzes WHERE uqid = :uqid)");
    $selectQuizAttributes->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));

    $selectQuizInfo = $conn->prepare("SELECT qname, type, create_time, code
                                            FROM quizzes
                                            WHERE uqid = :uqid");
    $selectQuizInfo->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));

    $quiz_attr = $selectQuizAttributes->fetchAll(PDO::FETCH_ASSOC);
    $quiz_info = $selectQuizInfo->fetch(PDO::FETCH_ASSOC);

    $marks = 0;
    $mark = array();
    $question_count = 0;
    $current_question = null;
    $question_types = array(
      "MCQ" => array("Multiple-Choice-Single-Correct-Answer Question{PLURAL}. Tick the box with the most appropriate option.", ""),
      "MCMQ" => array("Multiple-Choice-Multiple-Correct-Answers Question{PLURAL}. Tick all the boxes with appropriate options.", ""),
      "TF" => array("True/False Question{PLURAL}. Tick the box with the most appropriate option.", ""),
      "D" => array("Descriptive Question{PLURAL}. Write your answer in the space{PLURAL} provided.", "")
    );
    foreach ($quiz_attr as $q_index => $q_attr) {
      if ($current_question != $q_attr["question_number"]) {
        $mark[$q_attr["question_number"]] = 0;
        $question_count += 1;
        $current_question = $q_attr["question_number"];

        if (empty($question_types[$q_attr["type"]][1]))
          $question_types[$q_attr["type"]][1] = $q_attr["question_number"];
        else
          $question_types[$q_attr["type"]][1] = $question_types[$q_attr["type"]][1].", ".$q_attr["question_number"];
      }

      if ($q_attr["isanswer"] == 1 || $q_attr["option_number"] == 0) {
        $marks += $q_attr["weightage"];
        $mark[$q_attr["question_number"]] += $q_attr["weightage"];
      }
    }

    require_once "../fpdf/fpdf.php";

    class PDF extends FPDF {
      function CheckPageBreak($h) {
        if($this->GetY()+$h>$this->PageBreakTrigger)
          $this->AddPage($this->CurOrientation);
      }

      function header() {
        $margin = 10;
        $this->Rect($margin, $margin, $this->GetPageWidth() - 2 * $margin, $this->GetPageHeight() - 2 * $margin);
        $this->SetY(3);
        $this->SetFont('Helvetica', 'IB', 9);
        $this->Cell($this->GetStringWidth("Date: "), 10, "Date: ", 0, 0, 'L');
        $this->SetFont('Helvetica', 'I', 9);
        $this->Cell(0, 10, date("l, jS F, Y", time()), 0, 0, 'L');

        $this->SetFont('Helvetica', 'I', 9);
        $this->SetX($this->GetX() - $this->GetStringWidth("This Quiz is created using  Quizzee!"));
        $this->Cell($this->GetStringWidth("This Quiz is created using "), 10, "This Quiz is created using ", 0, 0, 'R');
        $this->SetFont('Helvetica', 'IB', 9);
        $this->Cell(0, 10, "Quizzee!", 0, 1, 'R');

      }

      function footer() {
        $this->SetY(-10);
        $this->SetFont('Helvetica', 'I', 9);
        $this->Cell(0, 10, 'Page '.$this->PageNo()." of {nb}", 0, 1, 'C');

        $this->SetY(-17);
        $this->SetFont('Helvetica', 'I', 8);
        $this->Cell(0, 10, "For any queries or issues regarding the Quiz, contact the Quiz Administrator. Quizzee will not be held responsible for any issues.", 0, 1, 'C');
      }
    }
    $pdf = new PDF();

    $pdf->SetAuthor($_SESSION['NAME']);
    $pdf->SetCreator("Quizzee");
    $pdf->SetKeywords("Quizzee ".date("l, jS F, Y", time())." ");
    $pdf->SetSubject(mb_strtoupper($quiz_info['qname']));

    $pdf->SetMargins(20, 20);
    $pdf->SetLineWidth(0.5);
    $pdf->AddPage();
    $pdf->SetY($pdf->GetY() + 2);
    $pdf->SetTitle(mb_strtoupper($quiz_info['qname']));
    $pdf->SetFont('Helvetica', 'B', 28);

    $pdf->Cell(0, 20, mb_strtoupper($quiz_info['qname']), 0, 1, 'C');

    $pdf->SetY($pdf->GetY() - 10);
    $pdf->SetFont('Helvetica', 'I', 11);
    $pdf->Cell(0, 20, "Created by, ".$_SESSION['NAME'], 0, 1, 'C');

    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetY($pdf->GetY() - 8);
    $pdf->Cell($pdf->GetStringWidth("Max. Marks:"), 15, "Max. Marks:", 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 12);
    $pdf->Cell(0, 15, " ".$marks." mark".(($marks == 1)? '':'s'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetX($pdf->GetX() - $pdf->GetStringWidth("Duration: Unspecified"));
    $pdf->Cell($pdf->GetStringWidth("Duration: "), 15, "Duration:", 0, 0, 'R');
    $pdf->SetFont('Helvetica', '', 12);
    $pdf->Cell(0, 15, "Unspecified", 0, 1, 'R');
    $pdf->SetFont('Helvetica', 'BU', 14);
    $pdf->Cell(0, 15, "INSTRUCTIONS", 0, 1, 'C');

    $pdf->SetFont('Helvetica', 'I', 11);
    $pdf->SetY($pdf->GetY() - 4);
    $pdf->Cell($pdf->GetStringWidth("1. "), 15, "1. ", 0, 0);
    $pdf->Cell($pdf->GetStringWidth("This Quiz contains "), 15, "This Quiz contains ", 0, 0);
    $pdf->Cell($pdf->GetStringWidth($question_count), 15, $question_count, 0, 0);
    $pdf->Cell(0, 15, " question".(($question_count > 1)? 's':'').". Please read and answer".(($question_count > 1)? ' all':'')." the question".(($question_count > 1)? 's':'')." carefully!", 0, 1);
    $pdf->SetY($pdf->GetY() - 7);
    $pdf->Cell($pdf->GetStringWidth("2. "), 15, "2. ", 0, 0);
    $pdf->Cell(0, 15, "'Extra Space' is provided at the end for calculations or extra writing space.", 0, 1, "L");

    $pdf->SetY($pdf->GetY() - 3);
    $ins_index = 3;
    foreach ($question_types as $type => $value) {
      if(!empty($value[1])) {
        $count = count(explode(', ', $value[1]));
        $instruction = "Question{PLURAL} ".$value[1];
        $instruction = $instruction.(($count > 1)? " are ":" is a ").$value[0];
        $pdf->Cell($pdf->GetStringWidth("1. "), 8, $ins_index.". ", 0, 0, "L");
        $pdf->MultiCell(0, 8, str_replace("{PLURAL}", (($count > 1)? "s":""), $instruction), 0, "L");
        $ins_index += 1;
      }
    }

    $pdf->SetFont('Helvetica', 'BU', 14);
    $pdf->SetY($pdf->GetY() + 5);
    $pdf->Cell(0, 15, "QUESTIONS", 0, 1, "C");
    $pdf->SetY($pdf->GetY() - 6);

    $option_offset = 8;

    $current_question = null;
    foreach ($quiz_attr as $q_index => $q_attr) {
      if ($current_question != $q_attr['question_number']) {
        $pdf->CheckPageBreak(30);

        $pdf->Cell(0, 10, "", 0, 1, "L");

        $current_question = $q_attr['question_number'];
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell($pdf->GetStringWidth("12. "), 7, $current_question.'. ', 0, 0);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(0, 7, $q_attr['question_desc']." (".$mark[$current_question]." mark".(($mark[$current_question] == 1)? '':'s').")", 0, "L");

        $pdf->SetFont('Helvetica', 'I', 11);
        if ($q_attr['type'] == "D") {
          $pdf->Cell(0, 15, "Write your answer in the space provided.", 0, 1, "L");
        } else if ($q_attr['type'] == "MCQ") {
          $pdf->Cell(0, 15, "Choose the most appropriate option.", 0, 1, "L");
        } else if ($q_attr['type'] == "MCMQ") {
          $pdf->Cell(0, 15, "Choose all appropriate options.", 0, 1, "L");
        } else if ($q_attr['type'] == "TF") {
          $pdf->Cell(0, 15, "True or False?", 0, 1, "L");
        }

        $pdf->SetFont('Helvetica', '', 12);
      }

      if ($q_attr['option_number'] == 0) {
        $pdf->SetLineWidth(0.3);
        $pdf->Cell(0, 80, "", 1, 1);
        $pdf->SetFont('Helvetica', 'I', 9);
        $pdf->SetY($pdf->GetY() - 5);
        $pdf->Cell(0, 5, "Space for answer", 0, 1, "C");
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetLineWidth(0.5);
      } else {
        $pdf->CheckPageBreak(9);
        $pdf->SetX($pdf->GetX() + $option_offset);

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Rect($x, $y + 2, 4, 4);
        $pdf->SetX($pdf->GetX() + 7);
        $pdf->MultiCell($pdf->GetPageWidth() - 2 * $option_offset, 9, $q_attr['option_desc'], 0, "L");
      }
    }

    $pdf->AddPage();
    $pdf->SetFont('Helvetica', 'IU', 11);
    $pdf->Cell(0, 5, "Extra Space", 0, 1, "C");
    $pdf->AliasNbPages();

    ob_clean();
    $pdf->Output("I", mb_strtoupper($quiz_info['qname'])." ".date("d-m-Y", time()).".pdf");
  }
?>
