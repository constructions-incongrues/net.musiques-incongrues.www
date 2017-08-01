<?php
  header("Content-type: text/css; charset: UTF-8");
  $imgSize = 25;
  $hiddenimgSize = $imgSize -2;
  $borderRadius = ceil($imgSize/5);
  $margin = ceil($imgSize/10)*5;
?>
@keyframes discussants {
  from {margin-right:-<?= $margin ?>px;}
  to {margin-right:1px;}
}

.DiscussantsContainer, .HiddenDiscussantsContainer {
  margin:0;
  padding:0;
}

.DiscussantsContainer img {
  width:<?= $imgSize ?>px;
  height:<?= $imgSize ?>px;
  -webkit-border-radius:<?= $borderRadius ?>px;
  -moz-border-radius:<?= $borderRadius ?>px;
  border-radius:<?= $borderRadius ?>px;
}

.HiddenDiscussantsContainer {
  display:inline;
}

.DiscussantsContainer li {
  display:inline;
  margin-right:-<?= $margin ?>px;
}

.HiddenDiscussantsContainer li {
  margin-right:-<?= $hiddenimgSize ?>px;
}

.DiscussantsContainer:hover li {
  animation-name:discussants;
  animation-delay:0.2s;
  animation-duration:0.2s;
  animation-fill-mode:forwards;
  animation-timing-function:ease-out;
}