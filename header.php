<div id="header">
    <?php
    $numNounPossibilities = 19;
    $noun1 = rand(1, $numNounPossibilities);
    do
    {
        $noun2 = rand(1, $numNounPossibilities);
    } while ($noun2 == $noun1);
    
    echo "<img src='http://chad-oh.com/images/banner/header_chad.png' width='254px' height='164px' alt='Chad' />";
    echo "<img src='http://chad-oh.com/images/banner/punctuation_chad/banner_punctuation_chad_".$punc1.".png' width='74px' height='164px' alt='.' />";
    echo "<img src='http://chad-oh.com/images/banner/header_oh.png' width='218px' height='164px' alt='Oh' />";
    echo "<img src='http://chad-oh.com/images/banner/punctuation_oh/banner_punctuation_oh_".$punc2.".png' width='154px' height='164px' alt='!' />";
    echo "<img src='http://chad-oh.com/images/banner/header_corn.png' width='395px' height='105px'/>";
    echo "<img src='http://chad-oh.com/images/banner/subtitle_1/banner_subtitle_1_".$noun1.".png' width='114px' height='105px' alt='showings'/>";
    echo "<img src='http://chad-oh.com/images/banner/header_&.png' width='25px' height='105px' alt='&'/>";
    echo "<img src='http://chad-oh.com/images/banner/subtitle_2/banner_subtitle_2_".$noun2.".png' width='166px' height='105px' alt='tellings' />";
    ?>
</div>
