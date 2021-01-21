import Vue from "vue";
import Notifications from 'vue-notification';

Vue.use(Notifications);

let helper = {
    toggleLoadingScreen(showOrHide) {
        if (showOrHide) {
            let isLoadingActive = document.getElementsByClassName('loading');
            if (isLoadingActive.length > 0) {
                return;
            }

            let elem = document.createElement('div');
            elem.className = 'loading';
            elem.id = 'loading';

            elem.innerHTML = `
            <style>
            .loading {
                width: 100%;
                height: 200px;
                background-color: #00000020;
                width: 100vw;
                height: 100vh;
                position: absolute;
                top: 0;
                left: 0;
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
    }
};

export default helper;