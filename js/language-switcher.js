(function ($, Drupal) {
  Drupal.behaviors.acdwebLanguageMobile = {
    attach: function (context, settings) {
      // Target the link specifically
      var $trigger = $(context).find('.language-switcher-wrapper .nav-link');

      // Bind both click and touchend for maximum mobile compatibility
      $trigger.off('click.acdLang touchend.acdLang').on('click.acdLang touchend.acdLang', function(e) {
        
        // Only on mobile (check if the toggle behaves like an accordion)
        if (window.innerWidth < 992) {
          
          // Find the menu relative to the clicked link
          var $menu = $(this).closest('.language-switcher-wrapper').find('.dropdown-menu');
          
          if ($menu.length) {
            // Prevent default link action
            e.preventDefault();
            // Stop bubbling so other theme JS doesn't close it immediately
            e.stopPropagation();

            // Toggle visual state
            if ($menu.hasClass('show')) {
              $menu.removeClass('show');
              $(this).attr('aria-expanded', 'false');
            } else {
              $menu.addClass('show');
              $(this).attr('aria-expanded', 'true');
            }
          }
        }
      });
    }
  };
})(jQuery, Drupal);