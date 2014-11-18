<?php
// Injects the OSD Social Sharing JavaScript into the footer of non-admin pages
function osd_sms_js() {
    ?>
    <script>
        (function() {
            // Define variables
            var sizes = {
                "twitter": [520, 400],
                "linkedIn": [520, 475],
                "default": [520, 300],
            }

            // Initialize OSD SMS
            init();


            // Initialize OSD Social Media Sharing
            function init() {
                // Attach event listeners to buttons
                var osd_shares = document.querySelectorAll('.osd-sms-link');
                for (var i=0, l=osd_shares.length; i < l; i++) {
                    var platform = osd_shares[i].getAttribute('data-platform');
                    var width = (sizes[platform] !== undefined) ? sizes[platform][0] : sizes["default"][0];
                    var height = (sizes[platform] !== undefined) ? sizes[platform][0] : sizes["default"][0];
                    osd_shares[i].setAttribute("data-width", width);
                    osd_shares[i].setAttribute("data-height", height);
                    osd_shares[i].addEventListener('click', osd_share);
                }

                // Set up pinterest modal
                if (document.querySelector('.osd-sms-link[data-platform=pinterest]') !== null) {
                    set_up_image_picker();
                }
            }


            // Fires the action appropriate to the link
            function osd_share(ev) {
                var platform = this.getAttribute('data-platform');
                if (platform === "pinterest") {
                    // Show the modal
                    if (document.querySelector('.osd-image-picker-modal').className.indexOf("osd-sms-no-images") !== -1) {
                        open_link(this, ev);
                    } else {
                        ev.preventDefault();
                        document.querySelector('.osd-image-picker-modal').className += " osd-sms-show";
                    }
                } else if (platform !== "email" && platform !== "print") {
                    open_link(this, ev);
                }
            }


            // Opens the share link
            function open_link(link, ev) {
                if (ev !== undefined) {
                    ev.preventDefault();
                }
                if (link.getAttribute("target") !== "_self") {
                    window.open(link.getAttribute("href"), link.getAttribute("data-platform"), "menubar=1,width="+link.getAttribute("data-width")+",height="+link.getAttribute("data-height")+",status=1,resizable=1");
                } else {
                    window.location = link.getAttribute("href");
                }
            }


            // Sets up image picker
            function set_up_image_picker() {
                // Attach image picker event to Pinterest button
                var modal = document.createElement("div");
                modal.className = "osd-image-picker-modal";
                modal.innerHTML = "<div class='osd-image-picker'><div class='osd-image-picker-list'></div></div>";
                document.body.appendChild(modal);
                modal.addEventListener("click", closeModal);

                var cont = document.querySelector('.osd-image-picker-list');
                var origImages = document.querySelectorAll('img');
                var images = [];

                // Loop through images and only get good types and images that aren't a part of OSD Social Media Sharing
                for (var i=0, l=origImages.length; i < l; i++) {
                    if (origImages[i].src.match(/\.(jpg|jpeg|png|svg|bmp|gif)$/) === null) {
                        continue;
                    } else if (origImages[i].parentElement.className.indexOf("osd-sms-link") !== -1) {
                        continue;
                    }
                    images.push(origImages[i]);
                }

                // If there are no good images, return
                if (images.length === 0) {
                    modal.className += " osd-sms-no-images";
                    return;
                }

                // Only append the images and attach events once
                for (var i=0, l=images.length; i < l; i++) {
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


            // Closes the modal
            function closeModal(ev) {
                var modal = document.querySelector('.osd-image-picker-modal');
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


            // Attach the image to the pinterest link
            function attachPinterestMedia() {
                var link = document.querySelector('.osd-sms-link[data-platform=pinterest]');
                link.href = link.href.replace(/&media=.*/, "&media=" + this.getAttribute("data-media"));
                closeModal();
                open_link(link);
            }
        })();
    </script>
    <?php
}
add_action('wp_footer', 'osd_sms_js');