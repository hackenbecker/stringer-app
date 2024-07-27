# StringerDB

A web based /app for racket stringers to keep track of jobs and string stock written in PHP.

- Tracks each string job.
- Ability to set customer defaults (Racket, tension, prestretch)
- Ability to add an image to each job
- Automatically tracks reel usage and warns on low stock.
- Keeps a track of total jobs.
- Shows outstanding money owed.
- Prints a standard 6x4 label for any thermal printer.
- Responsive, mobile friendly front-end.
  Tested and working with PHP Version 8.2.12

## Installation

You will need a working LAMP server

1.  Copy all of the files and folders to the
    public_html or web root folder on your web server.
2.  Create an empty MySQL database. Make a note of the DB name, user and password.
3.  Open up a web browser and navigate to your domain. The first page you should be greeted with is a database configuration page. Fill in the fields using the details you made a note of in the previous step. Tick the box to create all of the tables and sample data.
4.  When you first start the stringer app you will only be able to view certain pages.
    All other pages will require you to login before they can be viewed.
    Select login from the menu. The default login is admin and the password is $Admin001
5.  Click on the account icon from the main menu. Here you will see your account details. Change the password on the account by clicking reset password.
6.  Go to settings on the main menu. Set your currency and units to suit your location.
7.  Next go to settings and check that the reel lengths you have in stock are present in the list.
    Most have been added, but you may need to add more.
8.  The admin is a super user. You may wish to add another user that has less privileges. Go to settings "User accounts" to add more users and set the passwords.
9.  Click settings "Payment account details" These are the bank account details that will be printed on the label. These should reflect the account you wish to get paid into.
10. Lastly set your domain name. This should be yourdomain.com and should not have any https prefixes. This ensures the QR code is setup properly on the label once its printed. If you have created sub domains on your hosting site, your domain name must reflect this.

## Instructions for use

Its fairly straightforward to use. Full instructions are accessible via help once installed or this link
https://creative-it.co.uk/help.php

## Contributing

Pull requests are welcome. Please let me know if you decide to use it. If you have any suggestions or improvements email me at hackenbecker@gmail.com

## License

[MIT](https://choosealicense.com/licenses/mit/)
