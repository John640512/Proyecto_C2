<?php
function sanitizeInput($data) {
  return htmlspecialchars(stripslashes(trim($data)));
}

function validateDate($date) {
  return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}

function validateTime($time) {
  return (bool) preg_match('/^\d{2}:\d{2}$/', $time);
}