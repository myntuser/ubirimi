<?php
    use Ubirimi\Calendar\Repository\CalendarEventReminderPeriod;
    use Ubirimi\Calendar\Repository\CalendarReminderType;
    use Ubirimi\Util;

    require_once __DIR__ . '/_header.php';
?>
<body>

    <?php require_once __DIR__ . '/_menu.php'; ?>
    <div class="pageContent">

        <?php Util::renderBreadCrumb('<a class="linkNoUnderline" href="/calendar/calendars">Calendars</a> > ' . $calendar['name'] . ' > Settings') ?>
        <div class="headerPageText">Default Reminders</div>
        <hr size="1" />
        <form name="edit_status" action="/calendar/settings/<?php echo $calendarId ?>" method="post">
            <div id="content_event_reminders">
                <?php if ($defaultReminders): ?>

                    <?php while ($defaultReminders && $defaultReminder = $defaultReminders->fetch_array(MYSQLI_ASSOC)): ?>
                        <div id="reminder_content_<?php echo $defaultReminder['id'] ?>">
                            <span>By default, remind me via</span>
                            <select name="reminder_type_<?php echo $defaultReminder['id'] ?>" class="inputTextCombo">
                                <option value="<?php echo CalendarReminderType::REMINDER_EMAIL ?>">Email</option>
                            </select>
                            <input type="text" value="<?php echo $defaultReminder['value'] ?>" name="value_reminder_<?php echo $defaultReminder['id'] ?>" style="width: 50px;" />
                            <select name="reminder_period_<?php echo $defaultReminder['id'] ?>" class="inputTextCombo">
                                <option <?php
                                    if ($defaultReminder['cal_event_reminder_period_id'] == CalendarEventReminderPeriod::PERIOD_MINUTE) echo 'selected="selected"' ?> value="<?php echo CalendarEventReminderPeriod::PERIOD_MINUTE ?>">minutes</option>
                                <option <?php if ($defaultReminder['cal_event_reminder_period_id'] == CalendarEventReminderPeriod::PERIOD_HOUR) echo 'selected="selected"' ?> value="<?php echo CalendarEventReminderPeriod::PERIOD_HOUR ?>">hours</option>
                                <option <?php
                                    if ($defaultReminder['cal_event_reminder_period_id'] == CalendarEventReminderPeriod::PERIOD_DAY) echo 'selected="selected"' ?> value="<?php echo CalendarEventReminderPeriod::PERIOD_DAY ?>">days</option>
                                <option <?php if ($defaultReminder['cal_event_reminder_period_id'] == CalendarEventReminderPeriod::PERIOD_WEEK) echo 'selected="selected"' ?> value="<?php
                                    echo CalendarEventReminderPeriod::PERIOD_WEEK ?>">weeks</option>
                            </select>
                            <span>before each event</span>
                            <img src="/img/delete.png" id="delete_calendar_reminder_<?php echo $defaultReminder['id'] ?>" title="Delete reminder" />
                            <br />
                        </div>
                    <?php endwhile ?>
                <?php else: ?>
                    <div>There are no default reminders set for this calendar.</div>
                <?php endif ?>
            </div>
            <a href="#" id="event_add_reminder">Add a reminder</a>

            <hr size="1" />
            <button type="submit" name="edit_calendar_settings" class="btn ubirimi-btn"><i class="icon-edit"></i> Update Settings</button>
            <a class="btn ubirimi-btn" href="/calendar/calendars">Cancel</a>
        </form>
    </div>
    <?php require_once __DIR__ . '/_footer.php' ?>
</body>
</html>