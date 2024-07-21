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

1. Copy all of the files and folders to the
   public_html or web root folder on your web server.
2. Create an empty MySQL database.
3. Import the included SQL file. This should create all of the required tables and includes some sample data.
4. Edit Connections/wcba.php to reflect your database details
5. The default login is admin and password is $Admin001

![ScreenShot](./screenshots/screenshot1.png)

## Instructions for use

Its fairly straightforward to use. I will write a full set of instructions but am just initially uploading this readme.

## Contributing

Pull requests are welcome. Please let me know if you decide to use it. If you have any suggestions or improvements email me at hackenbecker@gmail.com

## License

[MIT](https://choosealicense.com/licenses/mit/)
