
    /**
    * Open Source Initiative OSI - The MIT License (MIT):Licensing
    *
    * The MIT License (MIT)
    * Copyright (c) 2012 Tim Reynolds
    *
    * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
    *
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    *
    * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    */


Magento Quick Config Module
===========================

I created this module to make working in the System -> Configuration area of the Magento Admin easier for my clients. With qsearch you get a text box that will search the labels and values of fields for any that match your input. Areas that don't match will be shaded out, allowing you to quickly and accurately navigate the configuration area. Simple, but effective.

![Screen Shot of QConfig](/screenshot.png?raw=true)

[Youtube Video showing how it works!](http://www.youtube.com/watch?v=t683rxYvEYg)

New Feature! Show Overrides In System Config
============================================

If you work in a Magento instance that has many websites and store views, or even if there are just a few, you may have been annoyed at not knowing if a value is overridden at a lower level in the configuration. This module now aims to solve that for you. If a value is overridden you will now see a new display next to the scope indicator for a field. See the image below.

![Screen Shot of Config Override](/screenshot2.png?raw=true)

The new indicator will show an underscore for each website or store view that does not override this configuration, and an 'X' for each one that does. You can mouse over the 'X' to see the title which indicates the exact store/view that overrides the value. If it is the same website/store you are currently on then it with be highlighted with a green background and bolt font weight.

Installation
------------

Clone the repository down to your computer. Copy the contents of the src/ directory into your Magento root directory. Then go into the admin and clear the config and layout caches.

This has been tested in Community 1.7 and Enterprise 1.12. If you have any issues please reach out, though as stated in the license this comes with no warranty. Please test in development before pushing to production!

TODO
----

There is no support yet for single/multi-select inputs that have a source model. I have worked on a few attempts at this, however I don't yet have a solution I am comfortable with. If you search for "enabled" you won't find much, as the actual value is "1" in the data. Additionally, searches for the Country/Region/Locale text names won't work, but if you search for the short-code it will (en_us vs United States).

Shameless Plug
--------------

I hope you enjoy this module. I have a few other modules I want to give back to the community. If you enjoy this, and need any help on a commercial project please don't hesitate to reach out. I can be contacted at Reynolds.TimJ@gmail.com or on Twitter @razialx.

Motivation and Thanks
---------------------

As the Magento community has been amazing to me, I decided to give this back as some small token of appreciation. I have long wanted to write this, but was always busy. The final motivation came when Alan Storm (@alanstorm, http://alanstorm.com) released an excellent module for quickly navigating the Admin menu with your keyboard. You should also buy his e-book on Magento Layouts. I also want to thank other great community members (and forgive me, I will surely forget many): @VinaiKopp @fbrnc @sherrierohde @sparcksoft @kab8609 @benmarks @markshust @monocat @arush @b_ike @colinmollenhour @alistairstead @aschroder @cloudhead @zerkella @s3lf and many many more. Thanks for making this the best software community around!
