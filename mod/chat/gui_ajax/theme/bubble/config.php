<?php
$chattheme_cfg = new stdClass();
$chattheme_cfg->avatar = true;
$chattheme_cfg->align  = true;
$chattheme_cfg->event_message = <<<TEMPLATE
<div class="chat-event">
<span class="time">___time___</span>
<a target='_blank' href="___senderprofile___">___sender___</a>
<span class="event">___event___</span>
</div>
TEMPLATE;
$chattheme_cfg->user_message_left = <<<TEMPLATE
<div class='chat-message' ___tablealign___>
    <span class="picture" style="vertical-align:middle;">___avatar___</span>
    <span class="triangle-isosceles left">___message___</span>
</div>
<div ___tablealign___ class="sendertime">
    <span class="time">___time___</span>
    <span class="user">___sender___</span>
</div>
TEMPLATE;
$chattheme_cfg->user_message_right = <<<TEMPLATE
<div class='chat-message' ___tablealign___>
    <span class="triangle-isosceles right">___message___</span>
    <span class="picture" style="vertical-align:middle;">___avatar___</span>
</div>
<div ___tablealign___ class="sendertime">
    <span class="time">___time___</span>
    <span class="user">___sender___</span>
</div>
TEMPLATE;
