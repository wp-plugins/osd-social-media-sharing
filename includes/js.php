<?php
// Injects the OSD Social Sharing JavaScript into the footer of non-admin pages
function osd_sms_js() {
    ?>
    <script async='true'>
        (function() {
            // Attach events
            var osd_shares = document.querySelectorAll('.osd-sms-link');
            for (var i=0, l=osd_shares.length; i < l; i++) {
                osd_shares[i].addEventListener('click', osd_share);
            }

            // Open the share links
            function osd_share(ev) {
                var platform = this.getAttribute('data-platform');
                switch (platform) {
                    case 'twitter':
                        height = 400;
                        width = 520;
                        break;
                    case 'linkedIn': 
                        height = 475;
                        width = 520;
                        break;
                    default:
                        height = 300;
                        width = 520;
                }
                if (platform !== "email" && platform !== "print") {
                    ev.preventDefault();
                    window.open(this.getAttribute("href"), platform, "menubar=1,width="+width+",height="+height+",status=1,resizable=1");
                }
            }
        })();
    </script>
    <?php
}
add_action('wp_footer', 'osd_sms_js');