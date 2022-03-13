/**
 * Helpers Functions
 */
import moment from 'moment';
import {API_URL, PROJECT_URL} from "../common/config";

export function debugLog(value) {
    console.log('%c ' + moment().format('mm:ss') + ' / ' + value, 'background: #fff; color: #bada55')
    return null
}

export function randomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}


export function groupBy(array, key, sub_key) {
    const result = {}
    array.forEach(item => {
        if (!result[item[key][sub_key]]) {
            result[item[key][sub_key]] = []
        }
        result[item[key][sub_key]].push(item)
    })
    return result
}


export function nextFactory(context, middleware, index) {
    const subsequentMiddleware = middleware[index];
    if (!subsequentMiddleware) return context.next;

    return (...parameters) => {
        const nextMiddleware = nextFactory(context, middleware, index + 1);
        subsequentMiddleware({...context, next: nextMiddleware});
        context.next(...parameters);
    };
}


export function toFixedCustom(value, num = 6) {
    value = Number.parseFloat(parseFloat(value)).toFixed(num)
    return value
}

export function priceFormat(val) {

    Number.prototype.formatMoney = function (fractionDigits, decimal, separator) {
        fractionDigits = isNaN(fractionDigits = Math.abs(fractionDigits)) ? 2 : fractionDigits;
        decimal = typeof (decimal) === "undefined" ? "." : decimal;
        separator = typeof (separator) === "undefined" ? "," : separator;
        let number = this;
        let neg = number < 0 ? "-" : "";
        let wholePart = parseInt(number = Math.abs(+number || 0).toFixed(fractionDigits)) + "";
        let separtorIndex = (separtorIndex = wholePart.length) > 3 ? separtorIndex % 3 : 0;
        return neg +
            (separtorIndex ? wholePart.substr(0, separtorIndex) + separator : "") +
            wholePart.substr(separtorIndex).replace(/(\d{3})(?=\d)/g, "$1" + separator) +
            (fractionDigits ? decimal + Math.abs(number - wholePart).toFixed(fractionDigits).slice(2) : "");

    };
    return Number(val).formatMoney(6, ',', '.')
}

export function axiosConf(data, actionURL = 'api', cancelToken = false, file) {
    return {
        url: API_URL.toString() + actionURL.toString(),
        data: data,
        method: 'POST',
        headers: {
            "Authorization": "Basic Y2xpZW50OnNlY3JldA==",
            "Content-Type": file ? "multipart/form-data" : (Object.values(data).length > 0 ? "text/json" : "text/plain"),
            "Access-Control-Allow-Origin": PROJECT_URL.toString(),
            "Access-Control-Allow-Credentials": true,
            "Access-Control-Allow-Headers": "X-PINGOTHER, Content-Type",
            'Access-Control-Expose-Headers': 'Access-Token, Uid',
            'Access-Control-Allow-Methods': 'POST, GET, OPTIONS',
            'language': window.localStorage.getItem('language')
        },
        cancelToken: cancelToken,
        withCredentials: true,
        credentials: true,
    }
}

export function convertDate(date) {
    date = new Date(date);
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let dt = date.getDate();
    if (dt < 10) {
        dt = '0' + dt;
    }
    if (month < 10) {
        month = '0' + month;
    }
    return year + '-' + month + '-' + dt;
}


export function slugify(text, ampersand = 'and') {
    const a = 'àáäâèéëêìíïîòóöôùúüûñçßÿỳýœæŕśńṕẃǵǹḿǘẍźḧ'
    const b = 'aaaaeeeeiiiioooouuuuncsyyyoarsnpwgnmuxzh'
    const p = new RegExp(a.split('').join('|'), 'g')

    return text.toString().toLowerCase()
        .replace(/[\s_]+/g, '-')
        .replace(p, c =>
            b.charAt(a.indexOf(c)))
        .replace(/&/g, `-${ampersand}-`)
        .replace(/[^\w-]+/g, '')
        .replace(/--+/g, '-')
        .replace(/^-+|-+$/g, '')
}

export function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


export function insertNullCleaner(obj) {
    var newObj = {}
    for (const [key, value] of Object.entries(obj)) {
        if (value !== null) {
            newObj[key] = value
        }
    }
    return newObj
}


export function camelToUnderscore(key) {
    if (key.replace(/[^a-z]/g, "").length > 0) {
        let result = key.replaceAll(/([A-Z])/g, " $1")
        return result.replaceAll("_", " ").split(' ').join(' ').substring()
    } else {
        return key
    }
}


export function dateConvertAPI(date, special = "-", convert = false, newBlace = '') {
    if (convert === 'regex') {
        return /([0-9][0-9]).([0-9][0-9]).([0-9][0-9][0-9][0-9])/gm.exec(date).slice(1).reverse().join(newBlace)
    } else if (convert) {
        return date.split(special).reverse().join(newBlace)
    }
    return date.replaceAll(special, newBlace)
}

export function getTheDate(timestamp, format) {
    let time = timestamp * 1000;
    let formatDate = format ? format : 'MM-DD-YYYY';
    return moment(time).format(formatDate);
}

export function convertDateToTimeStamp(date, format) {
    let formatDate = format ? format : 'YYYY-MM-DD';
    return moment(date, formatDate).unix();
}


export function textTruncate(str, length, ending) {
    if (length == null) {
        length = 100;
    }
    if (ending == null) {
        ending = '...';
    }
    if (str.length > length) {
        return str.substring(0, length - ending.length) + ending;
    } else {
        return str;
    }
}

export function hexToRgbA(hex, alpha) {
    var c;
    if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
        c = hex.substring(1).split('');
        if (c.length === 3) {
            c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c = '0x' + c.join('');
        return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ',' + alpha + ')';
    }
    throw new Error('Bad Hex');
}


export function getCurrentAppLayout(router) {
    let location = router.history.current.fullPath;
    let path = location.split("/")
    return path[1];
}

export function checkMobile() {
    if (screen.width <= 600) {
        return "mobile"
    } else {
        return "desktop"
    }
}

export function escapeUnicode(str) {
    return str.replaceAll(/\n/g, "\\n")
        .replaceAll(/\r/g, "\\r")
        .replaceAll(/\t/g, "\\t")
        .replaceAll(String.fromCharCode(0x1f), "")
}

export function datePeriod(dates) {
    let s = new Date(dates[0])
    let e = new Date(dates[1])
    for (var a = [], d = new Date(s); d <= e; d.setDate(d.getDate() + 1)) {
        a.push(new Date(d));
    }
    return a.map((v) => v.toISOString().slice(0, 10));
}

export function getAge(fromdate, todate, text = true, mounth = false) {
    if (todate) {
        todate = new Date(todate);
    } else {
        todate = new Date();
    }
    let age = {}
    fromdate = new Date(fromdate)
    if (mounth) {
        let months;
        months = (todate.getFullYear() - fromdate.getFullYear()) * 12;
        months -= fromdate.getMonth();
        months += todate.getMonth();
        return months <= 0 ? 0 : months;
    }
    let y = [todate.getFullYear(), fromdate.getFullYear()],
        ydiff = y[0] - y[1],
        m = [todate.getMonth(), fromdate.getMonth()],
        mdiff = m[0] - m[1],
        d = [todate.getDate(), fromdate.getDate()],
        ddiff = d[0] - d[1];
    if (mdiff < 0 || (mdiff === 0 && ddiff < 0)) --ydiff;
    if (mdiff < 0) mdiff += 12;
    if (ddiff < 0) {
        fromdate.setMonth(m[1] + 1, 0);
        ddiff = fromdate.getDate() - d[1] + d[0];
        --mdiff;
    }
    if (text) {
        if (ydiff > 0) age['year'] = ydiff + " yıl"
        if (mdiff > 0) age['month'] = mdiff + " ay"
        if (ddiff > 0) age['day'] = ddiff + " gün"
        age = Object.values(age).join(" ")
    } else {
        if (ydiff > 0) age['year'] = ydiff
        if (mdiff > 0) age['month'] = mdiff
        if (ddiff > 0) age['day'] = ddiff
    }
    return age;
}

export function detectBrowser() {
    if (navigator.userAgent.indexOf("Opera") != -1 || navigator.userAgent.indexOf('OPR') != -1) {
        return 'opera';
    } else if (navigator.userAgent.indexOf("Chrome") != -1) {
        return 'chrome';
    } else if (navigator.userAgent.indexOf("Safari") != -1) {
        return 'safari';
    } else if (navigator.userAgent.indexOf("Firefox") != -1) {
        return 'firefox';
    } else if ((navigator.userAgent.indexOf("MSIE") != -1) || (!!document.documentMode == true)) {
        return 'ie';
    } else {
        return 'unknown';
    }
}

export function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return window.clipboardData.setData("Text", text);

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            return document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}
