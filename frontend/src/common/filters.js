import Vue from "vue";

export function prettyBytes(bytes) {
    if (typeof bytes !== 'number' || isNaN(bytes)) {
        throw new TypeError('Expected a number');
    }
    const units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    if (bytes < 1024) {
        return bytes + ' B';
    }
    const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    bytes = (bytes / Math.pow(1024, exponent)).toFixed(2) * 1;
    const unit = units[exponent];
    return `${bytes} ${unit}`;
}

Vue.filter('prettyBytes', prettyBytes);
