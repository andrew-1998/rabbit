<?php
function foo() {
static $count = 4;
return ++$count;
}
print foo();
print foo();
print foo();
?>