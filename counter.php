<?php
//Define http header as .png image
header('Content-type: image/png');

//Retrieve current visitor count from .txt file
$count = intval(file_get_contents("count.txt"));

//Create GD image
$im_width = 300; $im_height = 300; //width & height in px
$im = imagecreatetruecolor($im_width, $im_height);

//Make background transparent
imagecolortransparent($im, imagecolorallocate($im, 0, 0, 0));

//Use anti-aliasing
imageantialias($im, true);

//Colour of image
$w = imagecolorallocate($im, 0xFF, 0xFF, 0xFF); //white

$min = 50;  //Minimum coordinate of the initial square
$max = 250; //Maximum coordinate of the initial square
            //The initial square is parallel to the viewport

//Define array of coordinates for the vertices of the initial square like [x0, y0, x1, y1, ...]
$init_square = array($min, $min,
                     $max, $min,
                     $max, $max,
                     $min, $max);

//Draws 16 squares rotated at an angle of pi/32 radians to each other
//Each square represents a binary digit of the visitor count with the initial square being the right-most digit
//Squares that correspond to a 0 are skipped, giving the described effect
for($i = 0; $i < 0x10; $i++) if($count & (1 << $i)) imagepolygon($im, rotate_square($init_square, $i * pi() / 0x20), 4, $w);

//Write the count in numerals in the centre of the image (zero-padded to 5 chars), incrementing the counter itself simultaneously
imagettftext($im, 32, 0, ($im_width / 2) - 62, ($im_height / 2) + 16, $w, "DroidSansMono.ttf", substr("00000" . $count++, -5, 5));

//Update the .txt file with new count
file_put_contents("count.txt", $count);

//Print the image
imagepng($im);
imagedestroy($im);

/* Rotates an entire square
 * @param   {int[]}  $pt_array   Array with coordinates of the vertices [x0, y0, x1, y1, ...]
 * @param   {int[]}  $angle      Angle of rotation in radians
 * @param   {int}    $centre_x   x-coordinate of centre of rotation in px
 * @param   {int}    $centre_y   y-coordinate of centre of rotation in px
 * @return  {int[]}  $r          Array with coordinates of the rotated vertices
 */
function rotate_square($pt_array, $angle, $centre_x, $centre_y){
	//Partition array into sub-arrays of size 2 for each vertex
	$part = array_chunk($pt_array, 2);
	
	$r = array(); //return array = []
	
	//Loop through the vertices, rotating each one and appending it to $r
	for($i = 0; $i < count($part); $i++) $r = array_merge($r, rotate($part[$i], $angle, $centre_x, $centre_y));
	
	return $r;
}

/* Rotates a point in 2D space
 * @param   {int[]}  $pt_array   Array with coordinates of the point like [x, y]
 * @param   {int}    $angle      Angle of rotation in radians
 * @param   {int}    $centre_x   x-coordinate of centre of rotation in px
 * @param   {int}    $centre_y   y-coordinate of centre of rotation in px
 * @return  {int[]}              Array with rotated coordinates
 */
function rotate($pt_array, $angle, $centre_x, $centre_y){
	$pt_array[0] -= $centre_x;
	$pt_array[1] -= $centre_y;
	$X = $pt_array[0] * cos($angle) - $pt_array[1] * sin($angle); // x' = x cos(a) - y sin(a) 
	$Y = $pt_array[0] * sin($angle) + $pt_array[1] * cos($angle); // y' = x sin(a) + y cos(a)
	return array($X + $centre_x, $Y + $centre_y);
}
?>
