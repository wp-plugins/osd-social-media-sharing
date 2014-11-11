<?php
// Injects the OSD Social Sharing JavaScript into the footer of non-admin pages
function osd_sms_js() {
    ?>
    <script>
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
                if (platform !== "email" && platform !== "print" && platform !== "pinterest") {
                    if (this.getAttribute("target") !== "_self") {
                        ev.preventDefault();
                        window.open(this.getAttribute("href"), platform, "menubar=1,width="+width+",height="+height+",status=1,resizable=1");
                    }
                }
            }
        })();
    </script>
    <?php
}
add_action('wp_footer', 'osd_sms_js');



// Injects the OSD Social Sharing JavaScript for the pinterest button functionality
// Added to footer in OSD Social Share class
function osd_sms_pinterest_js() {
    ?>
    <div class='osd-image-picker-modal'>
        <div class='osd-image-picker'>
            <div class="osd-image-picker-list"></div>
        </div>
    </div>
    
    <script>
        (function() {
            // Attach image picker event to Pinterest button
            var modal = document.querySelector('.osd-image-picker-modal'); 
            var cont = document.querySelector('.osd-image-picker-list');
            var pinterestLink = document.querySelector('.osd-sms-link[data-platform=pinterest]');
            pinterestLink.addEventListener("click", osdImagePicker);
            modal.addEventListener("click", closeModal);

            // Create the image picker
            function osdImagePicker(ev) {
                var images = document.querySelectorAll('img');

                // Exit if there are no images
                if (images.length === 0) {
                    return;
                }

                // Prevent opening of link
                ev.preventDefault();

                // Only append the images and attach events once
                if (cont.innerHTML === "") {
                    for (var i=0, l=images.length; i < l; i++) {
                        if (images[i].src.match(/\.(jpg|jpeg|png|svg|bmp)$/) === null) {
                            continue;
                        }
                        var imageCont = document.createElement('div');
                        var image = document.createElement('div');
                        imageCont.className = "osd-image-picker-img-cont";
                        image.className = "osd-image-picker-img";
                        image.setAttribute("data-media", encodeURIComponent(images[i].src));
                        image.setAttribute("data-media-size", images[i].naturalWidth + " x " + images[i].naturalHeight);
                        image.style.backgroundImage = "url(" + images[i].src + ")";
                        imageCont.appendChild(image);
                        cont.appendChild(imageCont);
                        image.addEventListener('click', attachPinterestMedia);
                    }
                }

                // Show the modal
                modal.className += " osd-sms-show";
            }

            // Closes the modal
            function closeModal(ev) {
                if (ev !== undefined) {
                    var parent = ev.target;
                    var count = 0;
                    while (parent !== null && count < 3) {
                        if (parent.className.indexOf("osd-image-picker-list") !== -1) {
                            return;
                        }
                        parent = parent.parentElement;
                        count++;
                    } 
                }
                modal.className = modal.className.replace(" osd-sms-show", "");
            }

            // Attaches pinterest image media
            function attachPinterestMedia() {
                pinterestLink.href = pinterestLink.href.replace(/&media=.*/, "&media=" + this.getAttribute("data-media"));
                closeModal();
                if (pinterestLink.getAttribute("target") === "_self") {
                    window.location = pinterestLink.getAttribute("href");
                    return;
                }
                window.open(pinterestLink.getAttribute("href"), "pinterest", "menubar=1,width=300,height=520,status=1,resizable=1");
            }
        })();
    </script>
    <?php
}