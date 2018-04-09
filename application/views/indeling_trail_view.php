<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo "indeling_trail";
?>
<div id="article">
<h1><?= $title ?></h1>
<span class="date"><?= $date ?></span>
<span class="author"><?= $author ?></span>
<p><?php echo nl2br( str_replace("\n\n","</p><p>", $body )); ?></p>
</div>

