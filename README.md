## Blacklist Auto Updater für WordPress


Nur wenigen WordPress-Nutzern ist die WordPress-interne Kommentar-Blacklist bekannt. Im WordPress-Administrationsbereich unter „Einstellungen“ – „Diskussion“ ist die Blacklist beheimatet und nimmt Werte bzw. Wörter entgegen, die den Spam identifizieren sollen.

Zusätzlich zu Antispam-Plugins wie [Antispam Bee](http://antispambee.de) empfiehlt sich die Pflege einer Kommentar-Blacklist. Entweder man betreut die Liste manuell oder man greift auf eine globale, sehr ausführliche [Kommentar-Blacklist](https://github.com/splorp/wordpress-comment-blacklist) zu, die zudem recht aktuell gehalten wird.


Autoupdates der Blacklist
-------------------------

Damit die auf GitHub geführte Blacklist nicht täglich auf ihre Aktualität geprüft und händisch in WordPress übernommen werden muss, wurde das WordPress-Plugin „Blacklist Auto Updater“ entwickelt.

Funktionsweise? Wurde die Kommentar-Blacklist auf GitHub aktualisiert (neue Antispam-Werte sind hinzugefügt worden), liest das Plugin die Liste ein und aktualisiert die WordPress-Datenbank. Auf diese Weise ist der Datenbestand der Kommentar-Blacklist stets aktuell. Der Check erfolgt täglich.


Mindestvoraussetzungen
----------------------
* WordPress 3.8
* PHP 5.3


Inbetriebnahme
--------------

1. ZIP herunterladen
2. Den entpackten Ordner ins Plugin-Verzeichnis kopieren
3. Plugin aktivieren


Autor
-----
*Sergej Müller*
* [Google+](https://plus.google.com/110569673423509816572?rel=author)
* [Twitter](https://twitter.com/wpSEO)
* [Plugins](http://wpcoder.de)