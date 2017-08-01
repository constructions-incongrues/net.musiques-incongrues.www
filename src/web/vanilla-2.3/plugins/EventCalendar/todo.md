Important: Edit current source because model should return standard $Discussion object  

  
  
# TODO (order shows prio)    
List is old - must be worked over  
  
## create discussion  
done 1. insert fields for event date and event time  
done 2. save extra info to db  
current 3. allow only for category from settings  
4. validate input  
(X. change button text from "New Discussion" to "New Event")  
  
  
  
## read discussion  
done 1. show extra info (date and time) below <div class="Tabs HeadingTabs EventTabs FirstPage">  
  
  
## event input only in "right" category  
1. disable extra event fields per css or javascript if not the event category is chosen  
current 2. check for right category after POST  
  
  
## create category listings   
1. /categories/events/month/mm/yyyy  
2. /categories/events/week/dd/mm/yyyy  
3. /categories/events/day/dd/mm/yyyy  
4. /categories/events/ sort order should be by event date  
5. discussion title prefixed by eventdate: discussiontitle  
6. change button text from "New Discussion" to "New Event"  
  
  
## create modules  
1. upcoming X events (X should be configurable)  
2. calendar with link to daily view (link for each day with event) and monthly view (month is a link)  
<|      month     |>  
 1  2  3  4  5  6  7  
 8  9 10 11 12 13 14  
15 16 17 18 19 20 21  
22 23 24 25 26 27 28  
29 30 31  
  
  
## edit discussion  
done 1. show extra fields  
done 2. prefill the wih values from db  
done 3. save to db  
  
  
## delete discussion  
1. delete also from EventCalendar_* tables  
  
  
## config settings in dashboard. start with setting them manually :-/  
1. category number to be treated as event category  
  $Configuration['Plugins']['EventCalendar']['EventCategory'] = ???;  
2. module upcoming events: how much events to show  
  $Configuration['Plugins']['EventCalendar']['UpcomingEventsCount'] = ???;  
  
  
## possibility for cleanup  
OnDisable must show a warning!  
one of those in order not to lose data if plugin has to be disabled  
a) EventCalendar_* tables will only be deleteted when chosen in a special subsection of dashboard  
b) possibility to export and import events  
  
    
# ALREADY DONE!  
  
## structure  
1. create table for additional event info (EventCalendar_Event)  
  
