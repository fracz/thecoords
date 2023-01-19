import axios from "@/axios";
import EventBus from "@/common/event-bus";

export class CurrentUser {
    constructor() {
        this.synchronizeAuthState();
    }

    getUserData() {
        try {
            return JSON.parse(atob(this.getToken().split('.')[1]));
        } catch (e) {
            this.forget();
            return null;
        }
    }

    getToken() {
        return localStorage.getItem('_token');
    }

    synchronizeAuthState() {
        if (this.getToken()) {
            this.tokenPayload = this.getUserData();
        } else {
            this.tokenPayload = undefined;
        }
        if (this.tokenPayload) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.getToken();
        } else {
            delete axios.defaults.headers.common['Authorization'];
        }
        EventBus.$emit('user-token-changed');
    }

    handleNewToken(token) {
        localStorage.setItem('_token', token);
        this.validateToken();
    }

    forget() {
        localStorage.removeItem('_token');
        this.synchronizeAuthState();
    }

    validateToken() {
        if (this.getToken()) {
            this.synchronizeAuthState();
            return this.fetchUserData();
        } else {
            return Promise.resolve(false);
        }
    }

    fetchUserData() {
        return axios.get('users/current')
            .then(({data}) => {
                this.settings = data.settings;
                return data;
            })
            .catch(({response}) => {
                if (response.status === 401 || response.status === 404 || response.status === 0 || response.status >= 500) {
                    this.forget();
                }
            });
    }
}


