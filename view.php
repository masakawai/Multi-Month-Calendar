<?php defined('C5_EXECUTE') or die('Access Denied.');

if (!isset($calendar) || !is_object($calendar)) {
    $calendar = null;
}
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI);
    ?><div class="ccm-edit-mode-disabled-item"><?=t('Calendar disabled in edit mode.')?></div><?php
    $loc->popActiveContext();
} elseif ($calendar !== null && $permissions->canViewCalendar()) { ?>
    <div class="ccm-block-calendar-wrapper row" data-calendar="<?=$bID?>">
        <div class="calendar-item col-xs-4"></div>
        <div class="calendar-item col-xs-4"></div>
        <div class="calendar-item col-xs-4"></div>
    </div>


    <script>
        $(function() {
            // set header
            var HEADER = {
                default: {
                    left: '',
                    center: 'title',
                    right: ''
                },
                first: {
                    left: 'prev',
                    center: 'title',
                    right: ''
                },
                last: {
                    left: '',
                    center: 'title',
                    right: 'next'
                },
            };

            // get current month
            var m = moment();

            // multi calendar
            $('div[data-calendar=<?=$bID?>] .calendar-item').each(function(){
                var POSITION = 'default';
                if ($(this).is(":first-child")) { POSITION = 'first'; }
                if ($(this).is(":last-child")) { POSITION = 'last'; }

                $(this).fullCalendar({
                    defaultDate: m,
                    header: HEADER [ POSITION ],
                    locale: <?= json_encode(Localization::activeLanguage()); ?>,
                    views: {
                        listDay: { buttonText: '<?= t('list day'); ?>' },
                        listWeek: { buttonText: '<?= t('list week'); ?>' },
                        listMonth: { buttonText: '<?= t('list month'); ?>' },
                        listYear: { buttonText: '<?= t('list year'); ?>' }
                    },

                    <?php if ($defaultView) { ?>
                        defaultView: '<?= $defaultView; ?>',
                    <?php } ?>
                    <?php if ($navLinks) { ?>
                        navLinks: true,
                    <?php } ?>
                    <?php if ($eventLimit) { ?>
                        eventLimit: true,
                    <?php } ?>

                    events: '<?=$view->action('get_events')?>',

                    eventRender: function(event, element) {
                        <?php if ($controller->supportsLightbox()) { ?>
                            element.attr('href', '<?=rtrim(URL::route(array('/view_event', 'calendar'), $bID))?>/' + event.id).magnificPopup({
                                type: 'ajax',
                                callbacks: {
                                    beforeOpen: function () {
                                        // just a hack that adds mfp-anim class to markup
                                        this.st.mainClass = 'mfp-zoom';
                                    }
                                },
                                closeBtnInside: true,
                                closeOnContentClick: true,
                                midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
                            });
                        <?php } ?>
                    }
                });
                m = m.add(1, 'month'); // next
            }); // end - multi calendar

            // click "prev" then all calendar move to the next month.
            $('div[data-calendar=<?=$bID?>] .calendar-item .fc-prev-button').each(function(){
                $(this).click(function() {
                    var PARENT = $('div[data-calendar=<?=$bID?>] .calendar-item').index($(this).closest('.calendar-item'));
                    $('div[data-calendar=<?=$bID?>] .calendar-item').each(function(index){
                        if (index != PARENT) { // desable move - calendar with button clicked.
                            $(this).fullCalendar('prev');
                        }
                    });
                });
            });

            // click "next" then all calendar move to the next month.
            $('div[data-calendar=<?=$bID?>] .calendar-item .fc-next-button').each(function(){
                $(this).click(function() {
                    var PARENT = $('div[data-calendar=<?=$bID?>] .calendar-item').index($(this).closest('.calendar-item'));
                    $('div[data-calendar=<?=$bID?>] .calendar-item').each(function(index){
                        if (index != PARENT) { // desable move - calendar with button clicked.
                            $(this).fullCalendar('next');
                        }
                    });
                });
            });

        });
    </script>
<?php
} ?>
