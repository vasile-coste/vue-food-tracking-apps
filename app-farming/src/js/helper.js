import Vue from "vue";

/** default number of loading screens */
let numOfLoadingScreens = 0;

let helper = {
    toggleLoadingScreen(showOrHide) {
        if (showOrHide) {
            /** increment number of loading screens request */
            numOfLoadingScreens++;

            /** check to see if there is any loading screens active */
            let isLoadingActive = document.getElementsByClassName('loading');
            if (isLoadingActive.length > 0) {
                return;
            }

            /** add loading screen */
            let elem = document.createElement('div');
            elem.className = 'loading';
            elem.id = 'loading';

            elem.innerHTML = `
            <style>
            .loading {
                background-color: #00000020;
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1050;
            }
            
            .loading-element:before {
                content: "";
                position: absolute;
                top: 50%;
                left: 50%;
                margin-top: -50px;
                margin-left: -50px;
                border-top: 3px solid #009A7F;
                border-right: 3px solid transparent;
                border-radius: 50%;
                width: 100px;
                height: 100px;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
            </style>
            <div class="loading-element"></div>            
            `;

            document.body.appendChild(elem);
        } else {
            /** if number of active requests are bigger than 0 we will not remove the loading screen */
            numOfLoadingScreens--;
            if (numOfLoadingScreens > 0) {
                return;
            }

            /** check if there's any loading screens */
            let isLoadingActive = document.getElementsByClassName('loading');
            if (isLoadingActive.length == 0) {
                return;
            }
            document.getElementById('loading').remove();
        }
    },
    showWarning(msg) {
        Vue.notify({
            group: "app",
            text: msg,
            type: "error",
        });
    },
    showSuccess(msg) {
        Vue.notify({
            group: "app",
            text: msg,
            type: "success",
        });
    },
    getLocation() {
        if (!("geolocation" in navigator)) {
            this.showWarning("Geolocation is not available.");
            return;
        }
        let options = {
            enableHighAccuracy: true,
            /** timeout: 30 * 1000,*/
            /** maximumAge: 500 */
        }
        return new Promise(function (resolve) {
            /** navigator.geolocation.getCurrentPosition  watchPosition*/
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    let location = {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude
                    }
                    resolve(location);
                },
                (err) => {
                    helper.locationError(err);
                },
                options
            );
        });
    },
    locationError(error) {
        let msg = "";
        switch (error.code) {
            case error.PERMISSION_DENIED:
                msg = "User denied the request for Geolocation.";
                break;
            case error.POSITION_UNAVAILABLE:
                msg = "Location information is unavailable.";
                break;
            case error.TIMEOUT:
                msg = "The request to get user location timed out.";
                break;
            case error.UNKNOWN_ERROR:
                msg = "An unknown error occurred.";
                break;
        }
        helper.showWarning(msg);
    }
};

export default helper;