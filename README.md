# binary website counter
Website counter made using a PHP GD image that generates a specific configuration of between 0 and 16 squares (1 square = 1 bit) according to the binary representation of the current website visitor count. After the counter hits 65,535 the number will keep going up but the graphic will reset as if it were at 0 again, because binary.

Doesn't account for unique visitors, but could be easily modified to do so by storing IP addresses or using cookies.
