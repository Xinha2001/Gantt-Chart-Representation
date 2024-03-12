<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") { //POST method to check if form was submitted
    $taskNames = $_POST['taskName'];
    $startDates = $_POST['startDate'];
    $endDates = $_POST['endDate'];
    $dependencies = $_POST['dependency'];

    $minDate = min(array_map('strtotime', $startDates));
    $maxDate = max(array_map('strtotime', $endDates));
    $dateRange = ($maxDate - $minDate) / (60 * 60 * 24); 

    $svgWidth = 1000;
    $svgHeight = count($taskNames) * 60 + 40; 
    $dayWidth = $svgWidth / $dateRange; 

    header('Content-Type: text/html');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Gantt Chart</title><style>body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; } .container { text-align: center; } svg { margin-top: 20px; }</style></head><body><div class="container"><h2>Gantt Chart</h2><svg width="' . $svgWidth . '" height="' . $svgHeight . '" style="border: 1px solid #ccc;">';

    foreach ($taskNames as $index => $name) {
        $startOffset = (strtotime($startDates[$index]) - $minDate) / (60 * 60 * 24);
        $taskLength = (strtotime($endDates[$index]) - strtotime($startDates[$index])) / (60 * 60 * 24);
        $yPosition = $index * 60 + 20;

        echo '<rect x="' . ($startOffset * $dayWidth) . '" y="' . $yPosition . '" width="' . ($taskLength * $dayWidth) . '" height="40" style="fill:#4CAF50;stroke-width:1;stroke:#3e8e41"/>';
        echo '<text x="' . ($startOffset * $dayWidth + 5) . '" y="' . ($yPosition + 25) . '" fill="white" font-size="12">' . htmlspecialchars($name) . '</text>';

        if (!empty($dependencies[$index]) && isset($taskNames[$dependencies[$index] - 1])) {
            $depIndex = $dependencies[$index] - 1;
            $depEndOffset = (strtotime($endDates[$depIndex]) - $minDate) / (60 * 60 * 24);
            $depYPosition = $depIndex * 60 + 40;

            echo '<line x1="' . ($depEndOffset * $dayWidth) . '" y1="' . $depYPosition . '" x2="' . ($startOffset * $dayWidth) . '" y2="' . ($yPosition + 20) . '" style="stroke:#FF5722;stroke-width:2" />';

            echo '<polygon points="' . (($startOffset * $dayWidth) - 5) . ',' . ($yPosition + 15) . ' ' . (($startOffset * $dayWidth) + 5) . ',' . ($yPosition + 20) . ' ' . (($startOffset * $dayWidth) - 5) . ',' . ($yPosition + 25) . '" style="fill:#FF5722;" />';
        }
    }

    echo '</svg></div></body></html>';
}
?>
