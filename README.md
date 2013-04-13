# Basecamp for Status Board

I wanted to show the most recent events from my [Basecamp](https://basecamp.com/) project on the iPad app [Status Board](http://panic.com/statusboard/) from Panic.

![status board](http://groenewege.com/files/basecamp_statusboard.jpg)

This widget shows the most recent activity on your Basecamp project(s)

You can find the PHP program necessary for this widget on this [github page](https://github.com/groenewege/basecamp-dashboard).

Install the program on a PHP 5.3 server, and change the necessary configuration parameters in the `index.php` file.

    /**
    * CHANGE THESE SETTINGS TO YOUR BASECAMP ACCOUNT SETTINGS
    */
    $basecampAccountId = 'xxxxxxx';
    $basecampUsername = 'xxxxxxx';
    $basecampPassword = 'xxxxxxx';

Now you can add a Do-It-Yourself widget to your Status Board with the correct link.

Enjoy.