 Letter Avatar for PHP

Generate user avatar using name initials letter.

![letter-avatar](https://cloud.githubusercontent.com/assets/618412/12192012/835c7488-b60d-11e5-9276-d06f42d11a86.png)

Based on the [package](https://github.com/yohang88/letter-avatar) of same name by Yoga Hanggara

## Features
* Data URI image ready (also save as PNG/JPG).
* Works with several unicode blocks
* Set saturation and luminosity to get consistent color
* Customize size, shape: square, circle.
* Small, fast.

TODO: Get combining marks work for unicode characters

## Install

Via Composer

``` bash
$ composer require sivarajd/letter-avatar
```

### Implementation

``` php
<?php

use SivarajD\LetterAvatar\LetterAvatar;

$avatar = new LetterAvatar('Steven Spielberg');

// Square Shape, Size 64px, Saturation 80, Luminosity 50
$avatar = new LetterAvatar('Steven Spielberg', 'square', 64, 80, 50);

// Save Image As PNG/JPEG
$avatar->saveAs('path/to/filename');
$avatar->saveAs('path/to/filename', "image/jpeg");

```

``` html
<img src="<?php echo $avatar ?>" />
```
