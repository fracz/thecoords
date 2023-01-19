import Vue from 'vue'
import {library} from '@fortawesome/fontawesome-svg-core'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

import {
    faAngleLeft,
    faAngleRight,
    faAnglesRight,
    faArrowDown,
    faArrowUp,
    faCheck,
    faChevronDown,
    faChevronLeft,
    faChevronUp,
    faCircleCheck,
    faCircleXmark,
    faClipboardCheck,
    faClock,
    faCogs,
    faCopy,
    faEllipsisVertical,
    faExclamationCircle,
    faExclamationTriangle,
    faFileImport,
    faInfoCircle,
    faMagnifyingGlass,
    faPhone,
    faPlus,
    faRadiation,
    faRefresh,
    faSearch,
    faSignIn,
    faTag,
    faTimes,
    faTrash,
    faUpload,
} from '@fortawesome/free-solid-svg-icons'

library.add(faCheck, faCogs, faPhone, faEllipsisVertical, faChevronDown, faChevronUp, faSignIn, faAnglesRight, faTrash,
    faArrowUp, faArrowDown, faAngleLeft, faAngleRight, faPlus, faExclamationCircle, faExclamationTriangle, faTimes,
    faInfoCircle, faRadiation, faCircleCheck, faCircleXmark, faCopy, faFileImport, faClipboardCheck,
    faChevronLeft, faMagnifyingGlass, faClock, faRefresh, faSearch, faUpload, faTag);

/* add font awesome icon component */
Vue.component('font-awesome-icon', FontAwesomeIcon)
