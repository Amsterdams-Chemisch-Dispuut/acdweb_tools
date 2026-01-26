(function (Drupal) {
  Drupal.behaviors.initFullCalendar = {
    attach: function (context, settings) {
      
      // --- PART A: Initialize Calendar ---
      var calendarEl = context.querySelector('#calendar');

      if (calendarEl && !calendarEl.classList.contains('fc-initialized')) {
        var isMobile = window.innerWidth < 768;
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
          timeZone: 'Europe/Amsterdam',
          initialView: isMobile ? 'listMonth' : 'dayGridMonth',
          events: '/events.json',
          
          // 1. Set Week Start to Monday
          firstDay: 1, 
          
          // 2. 24-Hour Time Format
          eventTimeFormat: {
            hour: '2-digit', minute: '2-digit', hour12: false, meridiem: false
          },
          slotLabelFormat: {
            hour: '2-digit', minute: '2-digit', hour12: false
          },

          // 3. Colors
          eventColor: 'orange', 
          eventTextColor: 'white',

          editable: true,
          selectable: true,
          nowIndicator: true,

          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
          },
          buttonText: {
            list: 'Lijst'
          }
        });

        calendar.render();
        calendarEl.classList.add('fc-initialized');
      }

      // --- 2. SETUP GOOGLE BUTTON ---
      // We search specifically for the ID. 
      // Using 'getElementById' is faster and safer than querySelector here.
      var googleBtn = document.getElementById('google-calendar-btn');

      // Check if button exists AND we haven't attached the listener yet
      if (googleBtn && !googleBtn.classList.contains('js-click-attached')) {
        
        googleBtn.addEventListener('click', async function() {
          var icsUrl = 'https://acdweb.nl/calendar.ics';
          
          // 1. Copy to clipboard
          try {
            await navigator.clipboard.writeText(icsUrl);
            alert('Calendar URL copied to clipboard!');
          } catch (err) {
            console.error('Failed to copy:', err);
            // Fallback for older browsers
            prompt("Copy this URL:", icsUrl);
          }

          // 2. Open Google Calendar
          // We use a slight delay to ensure the clipboard action finishes
          setTimeout(function() {
             window.open('https://calendar.google.com/calendar/u/0/r/settings/addbyurl', '_blank');
          }, 100);
        });

        // Mark as attached so we don't do it twice
        googleBtn.classList.add('js-click-attached');
      }

    }
  };
})(Drupal);