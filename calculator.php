<?php
if (!function_exists('numberToWords')) {
    function numberToWords($number) {
        $ones = array(
            0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six",
            7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve",
            13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen",
            18 => "Eighteen", 19 => "Nineteen"
        );

        $tens = array(
            2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty",
            7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
        );

        $thousands = array("", "Thousand", "Million", "Billion", "Trillion");

        if ($number == 0) return "Zero";

        $words = "";
        $chunks = array();
        while ($number > 0) {
            $chunks[] = $number % 1000;
            $number = (int)($number / 1000);
        }

        $numChunks = count($chunks);
        for ($i = $numChunks - 1; $i >= 0; $i--) {
            if ($chunks[$i] > 0) {
                $chunkWords = "";
                $hundreds = (int)($chunks[$i] / 100);
                $tensOnes = $chunks[$i] % 100;

                if ($hundreds > 0) {
                    $chunkWords .= $ones[$hundreds] . " Hundred ";
                }

                if ($tensOnes > 0) {
                    if ($tensOnes < 20) {
                        $chunkWords .= $ones[$tensOnes];
                    } else {
                        $chunkWords .= $tens[(int)($tensOnes / 10)];
                        if ($tensOnes % 10 > 0) {
                            $chunkWords .= " " . $ones[$tensOnes % 10];
                        }
                    }
                }

                if ($i > 0) {
                    $chunkWords .= " " . $thousands[$i];
                }

                $words .= $chunkWords . " ";
            }
        }

        return trim($words);
    }
}

if (!function_exists('numberToKhmerWords')) {
    function numberToKhmerWords($number) {
        $onesKhmer = array("០", "មួយ", "ពីរ", "បី", "បួន", "ប្រាំ", "ប្រាំមួយ", "ប្រាំពីរ", "ប្រាំបី", "ប្រាំបួន");
        $tensKhmer = array("", "ដប់", "ម្ភៃ", "សាមសិប", "សែសិប", "ហាសិប", "ហុកសិប", "ចិតសិប", "ប៉ែតសិប", "កៅសិប");
        $thousandsKhmer = array("", "ពាន់", "លាន", "ប៊ីលាន", "ទ្រីលាន");

        if ($number == 0) return "សូន្យ";

        $words = "";
        $chunks = array();
        while ($number > 0) {
            $chunks[] = $number % 1000;
            $number = (int)($number / 1000);
        }

        $numChunks = count($chunks);
        for ($i = $numChunks - 1; $i >= 0; $i--) {
            if ($chunks[$i] > 0) {
                $chunkWords = "";
                $hundreds = (int)($chunks[$i] / 100);
                $tensOnes = $chunks[$i] % 100;

                if ($hundreds > 0) {
                    $chunkWords .= $onesKhmer[$hundreds] . " រយ ";
                }

                if ($tensOnes > 0) {
                    if ($tensOnes < 10) {
                        $chunkWords .= $onesKhmer[$tensOnes];
                    } else if ($tensOnes < 20) {
                        $chunkWords .= "ដប់" . ($tensOnes % 10 > 0 ? " " . $onesKhmer[$tensOnes % 10] : "");
                    } else {
                        $chunkWords .= $tensKhmer[(int)($tensOnes / 10)];
                        if ($tensOnes % 10 > 0) {
                            $chunkWords .= " " . $onesKhmer[$tensOnes % 10];
                        }
                    }
                }

                if ($i > 0) {
                    $chunkWords .= " " . $thousandsKhmer[$i];
                }

                $words .= $chunkWords . " ";
            }
        }

        return trim($words) . " រៀល";
    }
}

$output = "";
$rielInputDisplay = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rielInput = isset($_POST["riel"]) ? $_POST["riel"] : null;

    if ($rielInput !== null && is_numeric($rielInput)) {
        $rielInputDisplay = number_format($rielInput); // Format input number for display
        $englishWords = numberToWords($rielInput) . " Riel";
        $khmerWords = numberToKhmerWords($rielInput);
        $usd = number_format($rielInput / 4000, 2) . "$";

        $logEntry = "Riel: $rielInput, English: $englishWords, Khmer: $khmerWords, USD: $usd\n";
        if (file_put_contents("current_projects.txt", $logEntry, FILE_APPEND) === false) {
            $output .= "<p class='error-message'>Error writing to log file.</p>";
        }

        $output = "
        <div class='output-container'>
            <p><strong>INPUT NUMBER:</strong> <span class='output-value'>$rielInputDisplay</span></p>
            <p><strong>IN ENGLISH:</strong> <span class='output-value'>$englishWords</span></p>
            <p><strong>IN KHMER:</strong> <span class='output-value'>$khmerWords</span></p>
            <p><strong>IN USD:</strong> <span class='output-value'>$usd</span></p>
        </div>";
    } else {
        $output = "<p class='error-message'>Please enter a valid number.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number to Words Converter</title>
    <link href="https://fonts.googleapis.com/css2?family=Khmer&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Color Palette */
            --primary-blue: #007bff; /* A standard, professional blue */
            --secondary-accent: #28a745; /* A vibrant green for accents */
            --background-light-grey: #f4f7f6; /* A very light, subtle background */
            --text-dark-grey: #343a40; /* Dark grey for main text and headings */
            --text-medium-grey: #6c757d; /* Medium grey for secondary text */
            --border-light: #ced4da; /* Light grey for borders */
            --container-bg: #ffffff; /* White background for the main container */
            --shadow-color: rgba(0, 0, 0, 0.1); /* Soft shadow */
            --error-red: #dc3545; /* Standard error red */

            /* Gradients */
            --body-gradient-start: #e0f2f7; /* Lighter blue/teal */
            --body-gradient-end: #cce7ee; /* Slightly darker blue/teal */
        }

        body {
            font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, var(--body-gradient-start), var(--body-gradient-end));
            color: var(--text-dark-grey);
            text-align: center;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            box-sizing: border-box;
        }

        .container {
            background-color: var(--container-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px var(--shadow-color);
            width: 90%;
            max-width: 500px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border: 1px solid var(--border-light); /* Subtle border for definition */
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        h1 {
            color: var(--primary-blue);
            margin-bottom: 30px;
            font-size: 2.5em;
            letter-spacing: 0.8px;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05); /* Subtle text shadow */
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        input[type="text"] {
            padding: 15px;
            width: 100%;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-size: 1.1em;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--background-light-grey);
            color: var(--text-dark-grey);
        }

        input[type="text"]::placeholder {
            color: var(--text-medium-grey);
            opacity: 0.8;
        }

        input[type="text"]:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
            outline: none;
            background-color: #fff;
        }

        input[type="submit"] {
            padding: 15px 25px;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.15em;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        .output-container {
            background-color: var(--background-light-grey);
            border-left: 6px solid var(--secondary-accent);
            padding: 25px;
            margin-top: 35px;
            border-radius: 8px;
            text-align: left;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            line-height: 1.8;
        }

        .output-container p {
            margin-bottom: 10px;
            color: var(--text-medium-grey);
            font-size: 1.05em;
        }

        .output-container p:last-child {
            margin-bottom: 0;
        }

        .output-container strong {
            color: var(--text-dark-grey);
            font-weight: 600;
            min-width: 120px; /* Aligns the values better */
            display: inline-block;
        }

        .output-value {
            font-weight: normal;
            color: #333;
        }

        .error-message {
            color: var(--error-red);
            margin-top: 20px;
            font-weight: 500;
            font-size: 1.1em;
        }

        /* Specific styles for Khmer text */
        .output-container p strong:nth-of-type(3) + .output-value {
            font-family: 'Khmer', sans-serif;
            font-size: 1.25em;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Number to Words Converter</h1>
        <form method="post">
            <input type="text" name="riel" id="riel" placeholder="Enter number here (e.g., 12345)" required>
            <input type="submit" value="Convert">
        </form>
        <?php echo $output; ?>
    </div>
</body>
</html>