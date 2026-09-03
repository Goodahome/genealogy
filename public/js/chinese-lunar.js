/**
 * Chinese calendar helpers: lunar date, solar terms, festivals (1900-2100).
 */
window.ChineseLunar = (function () {
    const LUNAR_INFO = [
        0x04bd8, 0x04ae0, 0x0a570, 0x054d5, 0x0d260, 0x0d950, 0x16554, 0x056a0, 0x09ad0, 0x055d2,
        0x04ae0, 0x0a5b6, 0x0a4d0, 0x0d250, 0x1d255, 0x0b540, 0x0d6a0, 0x0ada2, 0x095b0, 0x14977,
        0x04970, 0x0a4b0, 0x0b4b5, 0x06a50, 0x06d40, 0x1ab54, 0x02b60, 0x09570, 0x052f2, 0x04970,
        0x06566, 0x0d4a0, 0x0ea50, 0x06e95, 0x05ad0, 0x02b60, 0x186e3, 0x092e0, 0x1c8d7, 0x0c950,
        0x0d4a0, 0x1d8a6, 0x0b550, 0x056a0, 0x1a5b4, 0x025d0, 0x092d0, 0x0d2b2, 0x0a950, 0x0b557,
        0x06ca0, 0x0b550, 0x15355, 0x04da0, 0x0a5b0, 0x14573, 0x052b0, 0x0a9a8, 0x0e950, 0x06aa0,
        0x0aea6, 0x0ab50, 0x04b60, 0x0aae4, 0x0a570, 0x05260, 0x0f263, 0x0d950, 0x05b57, 0x056a0,
        0x096d0, 0x04dd5, 0x04ad0, 0x0a4d0, 0x0d4d4, 0x0d250, 0x0d558, 0x0b540, 0x0b6a0, 0x195a6,
        0x095b0, 0x049b0, 0x0a974, 0x0a4b0, 0x0b27a, 0x06a50, 0x06d40, 0x0af46, 0x0ab60, 0x09570,
        0x04af5, 0x04970, 0x064b0, 0x074a3, 0x0ea50, 0x06b58, 0x05ac0, 0x0ab60, 0x096d5, 0x092e0,
        0x0c960, 0x0d954, 0x0d4a0, 0x0da50, 0x07552, 0x056a0, 0x0abb7, 0x025d0, 0x092d0, 0x0cab5,
        0x0a950, 0x0b4a0, 0x0baa4, 0x0ad50, 0x055d9, 0x04ba0, 0x0a5b0, 0x15176, 0x052b0, 0x0a930,
        0x07954, 0x06aa0, 0x0ad50, 0x05b52, 0x04b60, 0x0a6e6, 0x0a4e0, 0x0d260, 0x0ea65, 0x0d530,
        0x05aa0, 0x076a3, 0x096d0, 0x04afb, 0x04ad0, 0x0a4d0, 0x1d0b6, 0x0d250, 0x0d520, 0x0dd45,
        0x0b5a0, 0x056d0, 0x055b2, 0x049b0, 0x0a577, 0x0a4b0, 0x0aa50, 0x1b255, 0x06d20, 0x0ada0,
        0x14b63, 0x09370, 0x049f8, 0x04970, 0x064b0, 0x168a6, 0x0ea50, 0x06b20, 0x1a6c4, 0x0aae0,
        0x0a2e0, 0x0d2e3, 0x0c960, 0x0d557, 0x0d4a0, 0x0da50, 0x05d55, 0x056a0, 0x0a6d0, 0x055d4,
        0x052d0, 0x0a9b8, 0x0a950, 0x0b4a0, 0x0b6a6, 0x0ad50, 0x055a0, 0x0aba4, 0x0a5b0, 0x052b0,
        0x0b273, 0x06930, 0x07337, 0x06aa0, 0x0ad50, 0x14b55, 0x04b60, 0x0a570, 0x054e4, 0x0d160,
        0x0e968, 0x0d520, 0x0daa0, 0x16aa6, 0x056d0, 0x04ae0, 0x0a9d4, 0x0a2d0, 0x0d150, 0x0f252,
        0x0d520,
    ];

    const TERM_INFO = [
        0, 21208, 42467, 63836, 85337, 107014, 128867, 150921, 173149, 195551, 218072, 240693,
        263343, 285989, 308563, 331033, 353350, 375494, 397447, 419210, 440795, 462224, 483532, 504758,
    ];

    const TERM_NAMES = [
        '小寒', '大寒', '立春', '雨水', '惊蛰', '春分', '清明', '谷雨',
        '立夏', '小满', '芒种', '夏至', '小暑', '大暑', '立秋', '处暑',
        '白露', '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至',
    ];

    const MONTHS = {
        1: '正月', 2: '二月', 3: '三月', 4: '四月', 5: '五月', 6: '六月',
        7: '七月', 8: '八月', 9: '九月', 10: '十月', 11: '冬月', 12: '腊月',
    };

    const DAYS = {
        1: '初一', 2: '初二', 3: '初三', 4: '初四', 5: '初五',
        6: '初六', 7: '初七', 8: '初八', 9: '初九', 10: '初十',
        11: '十一', 12: '十二', 13: '十三', 14: '十四', 15: '十五',
        16: '十六', 17: '十七', 18: '十八', 19: '十九', 20: '二十',
        21: '廿一', 22: '廿二', 23: '廿三', 24: '廿四', 25: '廿五',
        26: '廿六', 27: '廿七', 28: '廿八', 29: '廿九', 30: '三十',
    };

    const YEAR_DIGITS = {
        0: '零', 1: '一', 2: '二', 3: '三', 4: '四',
        5: '五', 6: '六', 7: '七', 8: '八', 9: '九',
    };

    // Solar fixed festivals / public holidays
    const SOLAR_FESTIVALS = {
        '1-1': '元旦',
        '2-14': '情人节',
        '3-8': '妇女节',
        '3-12': '植树节',
        '4-1': '愚人节',
        '5-1': '劳动节',
        '5-4': '青年节',
        '6-1': '儿童节',
        '7-1': '建党节',
        '8-1': '建军节',
        '9-10': '教师节',
        '10-1': '国庆节',
        '12-24': '平安夜',
        '12-25': '圣诞节',
    };

    // Lunar festivals (non-leap month)
    const LUNAR_FESTIVALS = {
        '1-1': '春节',
        '1-15': '元宵节',
        '2-2': '龙抬头',
        '5-5': '端午节',
        '7-7': '七夕',
        '7-15': '中元节',
        '8-15': '中秋节',
        '9-9': '重阳节',
        '12-8': '腊八节',
        '12-23': '小年',
        '12-24': '小年',
    };

    function leapMonth(year) {
        return LUNAR_INFO[year - 1900] & 0xf;
    }

    function leapDays(year) {
        if (leapMonth(year)) {
            return (LUNAR_INFO[year - 1900] & 0x10000) ? 30 : 29;
        }
        return 0;
    }

    function monthDays(year, month) {
        return (LUNAR_INFO[year - 1900] & (0x10000 >> month)) ? 30 : 29;
    }

    function yearDays(year) {
        let sum = 348;
        for (let i = 0x8000; i > 0x8; i >>= 1) {
            sum += (LUNAR_INFO[year - 1900] & i) ? 1 : 0;
        }
        return sum + leapDays(year);
    }

    function toChineseYear(year) {
        return String(year).split('').map((d) => YEAR_DIGITS[d] || d).join('');
    }

    function solarToLunar(year, month, day) {
        if (year < 1900 || year > 2100) {
            return null;
        }

        const base = Date.UTC(1900, 0, 31);
        const current = Date.UTC(year, month - 1, day);
        let offset = Math.floor((current - base) / 86400000);

        let lunarYear = 1900;
        let daysOfYear = 0;
        for (; lunarYear < 2101 && offset > 0; lunarYear++) {
            daysOfYear = yearDays(lunarYear);
            offset -= daysOfYear;
        }
        if (offset < 0) {
            offset += daysOfYear;
            lunarYear--;
        }

        const leap = leapMonth(lunarYear);
        let isLeap = false;
        let lunarMonth = 1;
        let daysOfMonth = 0;

        for (; lunarMonth < 13 && offset > 0; lunarMonth++) {
            if (leap > 0 && lunarMonth === leap + 1 && !isLeap) {
                lunarMonth--;
                isLeap = true;
                daysOfMonth = leapDays(lunarYear);
            } else {
                daysOfMonth = monthDays(lunarYear, lunarMonth);
            }

            if (isLeap && lunarMonth === leap + 1) {
                isLeap = false;
            }

            offset -= daysOfMonth;
        }

        if (offset === 0 && leap > 0 && lunarMonth === leap + 1) {
            if (isLeap) {
                isLeap = false;
            } else {
                isLeap = true;
                lunarMonth--;
            }
        }

        if (offset < 0) {
            offset += daysOfMonth;
            lunarMonth--;
        }

        return {
            year: lunarYear,
            month: lunarMonth,
            day: offset + 1,
            isLeap: isLeap,
        };
    }

    function getTermDate(year, termIndex) {
        const ms = 31556925974.7 * (year - 1900) + TERM_INFO[termIndex] * 60000;
        return new Date(ms + Date.UTC(1900, 0, 6, 2, 5));
    }

    function getSolarTerm(year, month, day) {
        for (let i = 0; i < 24; i++) {
            const termDate = getTermDate(year, i);
            if (
                termDate.getFullYear() === year &&
                termDate.getMonth() + 1 === month &&
                termDate.getDate() === day
            ) {
                return TERM_NAMES[i];
            }
        }
        return null;
    }

    function getFestivals(year, month, day, lunar) {
        const festivals = [];
        const solarKey = month + '-' + day;
        if (SOLAR_FESTIVALS[solarKey]) {
            festivals.push(SOLAR_FESTIVALS[solarKey]);
        }

        if (lunar && !lunar.isLeap) {
            const lunarKey = lunar.month + '-' + lunar.day;
            if (LUNAR_FESTIVALS[lunarKey]) {
                festivals.push(LUNAR_FESTIVALS[lunarKey]);
            }

            // 除夕：腊月最后一天
            if (lunar.month === 12) {
                const lastDay = monthDays(lunar.year, 12);
                if (lunar.day === lastDay) {
                    festivals.push('除夕');
                }
            }
        }

        return festivals;
    }

    function dayInfo(year, month, day) {
        const lunar = solarToLunar(year, month, day);
        const solarTerm = getSolarTerm(year, month, day);
        const festivals = getFestivals(year, month, day, lunar);
        const lunarText = lunar
            ? (lunar.day === 1
                ? ((lunar.isLeap ? '闰' : '') + (MONTHS[lunar.month] || (lunar.month + '月')))
                : (DAYS[lunar.day] || (lunar.day + '日')))
            : '';

        const holidayNames = ['元旦', '春节', '清明', '劳动节', '端午节', '中秋节', '国庆节', '除夕'];
        const isHoliday = Boolean(
            (solarTerm === '清明') ||
            festivals.some((name) => holidayNames.includes(name))
        );

        return {
            lunar: lunar,
            lunarText: lunarText,
            solarTerm: solarTerm,
            festivals: festivals,
            isHoliday: isHoliday,
            label: festivals[0] || solarTerm || lunarText,
        };
    }

    function format(date) {
        const lunar = solarToLunar(date.getFullYear(), date.getMonth() + 1, date.getDate());
        if (!lunar) {
            return '';
        }

        const monthName = (lunar.isLeap ? '闰' : '') + (MONTHS[lunar.month] || (lunar.month + '月'));
        const dayName = DAYS[lunar.day] || (lunar.day + '日');

        return '农历' + toChineseYear(lunar.year) + '年' + monthName + dayName;
    }

    return {
        solarToLunar: solarToLunar,
        format: format,
        dayInfo: dayInfo,
        months: MONTHS,
        days: DAYS,
    };
})();
