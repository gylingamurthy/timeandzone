# timeandzone

Task:-  To display the clock based on time zone selected in the Admin Configuration

a> Creates a custom module

b> In SRC folder created custom Form which will appear in the ACF

c> In SRC folder created custom block which assist to display the clock in the rendering page

d> jclockgmt library is used as a third party

e> in the admin config section it creates a configuration link with name "Configure Time and Zone"

f> Created a two dropdown field for country and timezone respectively
|----->CountryManagerInterface of Core is used to fill the respective dropdowns

g> custom block will appear in the block layout so that you can place it any page zone

h> The clock will get displayed in the page

i> cachebackendinterface of Core is used to invalidate the block seeting so it display the time with cache rebuilding

