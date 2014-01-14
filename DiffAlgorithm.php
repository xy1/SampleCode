<?php

// Diff algorithm
//
// Compares 2 text pages and shows the differences between them
// Performs similar function to the Linux/Unix "diff" utility
// Originally intended for a custom version-tracking system, but performance speed was poor, so an alternate approach was used
// Only taking 8 hours to write, it served as a good coding exercise
//
// phase 1 of 2
//
// it goes through string 1, letter by letter, finding the longest substring (starting from that position) contained anywhere in string 2.
// if it's also contained in string 2, it is referred to as a "common section".
// once it checks all positions and finds the longest "common section", it saves it (start point and length) to the 1st element in an array.
// it repeats this process, looking for the next-longest "common section" in the remaining portions of string 1.
// note that it skips over (and stops at) those portions of string 1 that have been already designated as "common sections".
// once it finds the next-longest "common section", it saves it to the next element in the array.
// it continues like this and stores all the "common sections" in an array.
// once it cannot find any more "common sections" (even 1 character in length), it stops iterating.
//
// phase 2 of 2
//
// once done iterating, it parses the original string 1 into words (defined by blank spaces or end of string).
// for each word, it notes the starting and ending position of that word.
// it looks at each word, and determines if that entire word falls inside any "common section" (based on its start and end points).
// if so, then the word is good.
// if not, them the word is bad.
// good words are left alone.
// bad words are replaced with capital letters.
//
// the function returns a translated string.
// this entire function is run twice: once to highlight (capitalize) the differences in string 1 and once to do so for string 2.

function HighlightDifferences( $oPageContent1, $oPageContent2 ) {
        $l = strlen($oPageContent1);

        $segments = array ();
        $segment_count = 0;

        while (1 == 1) {
                $i = 0;
                $j = 0;

                $longest_start = -1;
                $longest_length = 0;
                for ($i = 0; $i <= $l; $i++ ) {

                        // skip over common sections
                        $skip = 0;
                        foreach ( $segments as $s ) {
                                $tmp_start = substr($s, 0, strpos($s,"for"));
                                $tmp_length = substr($s, strpos($s,"for") + 3);
                                if ( $i >= $tmp_start && $i <= $tmp_start + $tmp_length ) { $skip = 1; }
                        }
                        if ( $skip == 1 ) { continue; }

                        $tmp_longest_start = 0;
                        $tmp_longest_length = 0;
                        for ($j = 1; $j <= ($l - $i); $j++ ) {

                                // stop if reached common section
                                $stop = 0;
                                foreach ( $segments as $s ) {
                                        $tmp_start = substr($s, 0, strpos($s,"for"));
                                        $tmp_length = substr($s, strpos($s,"for") + 3);
                                        if ( $j >= $tmp_start && $j <= $tmp_start + $tmp_length ) {
                                                $stop = 1;
                                        }
                                }

                                $chunk = substr($oPageContent1, $i, $j );
                                if ( strpos($oPageContent2,$chunk) !== FALSE ) {
                                        $tmp_longest_start = $i;
                                        $tmp_longest_length = strlen($chunk) - 1;
                                        if ( $tmp_longest_length > $longest_length ) {
                                                $longest_start = $tmp_longest_start;
                                                $longest_length = $tmp_longest_length;
                                        }
                                        if ( $stop == 1 ) { break; }
                                }
                                else { break; }
                        }
                }

                if ( $longest_start < 0 ) { break; }

                $segments[$segment_count] = $longest_start . "for" . $longest_length;
                $segment_count++;
        }

        // parse content word-by-word and see which words (if any) have changes in them
        $last_word_end = -1;
        for ($i = 0; $i <= $l; $i++ ) {
                if ( substr($oPageContent1, $i, 1 ) == ' ' || $i == $l ) { // end of word or end of string
                        $word_start = $last_word_end + 1;
                        $word_end = $i - 1;
                        $word_length = $word_end - $word_start + 1;
                        $word = substr($oPageContent1, $word_start, $word_length);
                        $last_word_end = $i;
                        $word_is_ok = 0;
                        foreach ( $segments as $s ) {
                                $tmp_start = substr($s, 0, strpos($s,"for"));
                                $tmp_length = substr($s, strpos($s,"for") + 3);
                                $tmp_end = $tmp_start + $tmp_length;
                                if ( $word_start >= $tmp_start && $word_end <= $tmp_end ) {
                                        $word_is_ok = 1;
                                }
                        }
        
                        if ( $word_is_ok == 0 && trim($word) > '' ) {
                                $oPageContent1 = substr($oPageContent1, 0, $word_start) .
                                        strtoupper( substr($oPageContent1, $word_start, $word_length) ) .
                                        substr($oPageContent1, $word_end + 1);
                        }
                }
        }

        return $oPageContent1;
}

$oPageContent1 = "oh the apple beta circus dog";
$oPageContent2 = "oh the pear beta circus cat";

$oPageContent1 = "BBB Smart Investing is a new consumer education program to provide investors with the necessary tools and resources to make informed decisions and avoid scams.";
$oPageContent2 = "BBB Smart Investing is a new corporate education program to provide investors with the necessary tools and resources to make informed decisions and avoid scams.";

echo "original 1: " . $oPageContent1 . "\n";
echo "original 2: " . $oPageContent2 . "\n";

$oPageContent1 = HighlightDifferences( $oPageContent1, $oPageContent2 );
$oPageContent2 = HighlightDifferences( $oPageContent2, $oPageContent1 );

echo "modified 1: " . $oPageContent1 . "\n";
echo "modified 2: " . $oPageContent2 . "\n";

?>
