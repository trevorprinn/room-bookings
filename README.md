# Room Bookings #

This is a web based room booking system, designed for a social club to manage its rooms. It requires a PHP server and a MySQL database. The client code should run in any reasonably up to date browser on desktop, tablet and phone, and uses Angular, jQuery and Bootstrap. It includes an api call to back up the database to the calling machine.

## Installation ##

1. Run the create-database.sql script on an empty MySQL schema.
2. Copy config-template.php to config.php and fill in the details of the database etc.
3. Copy the files to the server (eg in a "bookings" folder). 
4. Create a standard .htaccess file in the bookings folder to control access and allow logins.
5. Create another .htaccess file in the api folder to allow everyone access to it (if the wordpress plugin is in use).
6. Ensure the api/temp folder allows the webserver user read/write access (this is used by the backup facility).

## Database backup ##

There are 2 ways to download a database backup (including database definition and data):

1. From the Manage menu on the Nav Bar.
2. By sending a GET request to `http://yourserver/bookings/backup-db.php`.

Note that credentials set in the .htaccess file would need to be sent in the GET in the latter case.

For example, in C#

```C#
string url = "http://example.com/bookings/backup-db.php";
var req = (HttpWebRequest)WebRequest.Create(url);
req.Credentials = new NetworkCredential("boss", "password");
req.Method = "GET";
using (var resp = (HttpWebResponse)req.GetResponse()) 
using (var f = new FileStream(@"C:\downloads\backup.sql.gz", FileMode.Create)) {
	resp.GetResponseStream().CopyTo(f);
}
```

## Use ##

Before any bookings can be entered, at least one Room should be created, from the Manage menu on the Nav Bar. Facilities (such as PA, Projector etc) can also be created, and added to specific Rooms. Bookings for rooms can then be set to include or not the facilities available in the room. The Room colours are used on the Calendar display.

Bookers are the customers who are booking rooms. They can either be created in the Bookers pages or, when creating a new booking, by pressing the New button and entering a name. The latter will create a Booker record with just the name, that can be filled in later.

Everything else should be pretty straightforward.

## Wordpress plugin ##

There is also a Wordpress plugin available at https://github.com/trevorprinn/room-bookings-ui. This allows
some of the information about future bookings to be displayed on a Wordpress site, as a table
and/or a calendar, and allows users to add provisional bookings.
