/**
 * Chinese calendar helpers — adapter over 6tail/lunar-javascript (Solar / Lunar).
 * 覆盖约公元 1–9999 年，与主流农历站点同源算法。
 * 依赖：先加载 public/js/lunar.js（暴露全局 Solar、Lunar）。
 */
window.ChineseLunar = (function () {
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

    const HOLIDAY_KEYWORDS = ['元旦', '春节', '清明', '劳动', '端午', '中秋', '国庆', '除夕'];

    function ready() {
        return typeof Solar !== 'undefined' && typeof Lunar !== 'undefined';
    }

    function toLunarParts(lunar) {
        const month = lunar.getMonth();
        return {
            year: lunar.getYear(),
            month: Math.abs(month),
            day: lunar.getDay(),
            isLeap: month < 0,
        };
    }

    function solarToLunar(year, month, day) {
        if (!ready()) {
            return null;
        }
        try {
            return toLunarParts(Solar.fromYmd(Number(year), Number(month), Number(day)).getLunar());
        } catch (e) {
            return null;
        }
    }

    /**
     * @param {number} year
     * @param {number} month 1–12
     * @param {number} day
     * @param {boolean} [isLeap]
     */
    function lunarToSolar(year, month, day, isLeap) {
        if (!ready()) {
            return null;
        }
        try {
            const lunarMonth = isLeap ? -Math.abs(Number(month)) : Number(month);
            const solar = Lunar.fromYmd(Number(year), lunarMonth, Number(day)).getSolar();
            return {
                year: solar.getYear(),
                month: solar.getMonth(),
                day: solar.getDay(),
            };
        } catch (e) {
            return null;
        }
    }

    function collectFestivals(solar, lunar) {
        const names = [];
        const pushAll = (list) => {
            (list || []).forEach((name) => {
                if (name && !names.includes(name)) {
                    names.push(name);
                }
            });
        };
        pushAll(solar.getFestivals());
        pushAll(solar.getOtherFestivals());
        pushAll(lunar.getFestivals());
        pushAll(lunar.getOtherFestivals());
        return names;
    }

    function dayInfo(year, month, day) {
        if (!ready()) {
            return {
                lunar: null,
                lunarText: '',
                solarTerm: null,
                festivals: [],
                isHoliday: false,
                label: '',
            };
        }

        try {
            const solar = Solar.fromYmd(Number(year), Number(month), Number(day));
            const lunarObj = solar.getLunar();
            const lunar = toLunarParts(lunarObj);
            const solarTerm = lunarObj.getJieQi() || null;
            const festivals = collectFestivals(solar, lunarObj);
            const lunarText = lunar.day === 1
                ? ((lunar.isLeap ? '闰' : '') + (MONTHS[lunar.month] || (lunar.month + '月')))
                : (DAYS[lunar.day] || (lunar.day + '日'));

            const isHoliday = Boolean(
                (solarTerm === '清明') ||
                festivals.some((name) => HOLIDAY_KEYWORDS.some((key) => String(name).includes(key)))
            );

            return {
                lunar: lunar,
                lunarText: lunarText,
                solarTerm: solarTerm,
                festivals: festivals,
                isHoliday: isHoliday,
                label: festivals[0] || solarTerm || lunarText,
            };
        } catch (e) {
            return {
                lunar: null,
                lunarText: '',
                solarTerm: null,
                festivals: [],
                isHoliday: false,
                label: '',
            };
        }
    }

    function format(date) {
        if (!ready() || !date) {
            return '';
        }
        try {
            const lunar = Solar.fromDate(date instanceof Date ? date : new Date(date)).getLunar();
            const month = lunar.getMonth();
            const leap = month < 0 ? '闰' : '';
            const monthName = MONTHS[Math.abs(month)] || (Math.abs(month) + '月');
            const dayName = DAYS[lunar.getDay()] || (lunar.getDay() + '日');
            return '农历' + lunar.getYearInChinese() + '年' + leap + monthName + dayName;
        } catch (e) {
            return '';
        }
    }

    function leapMonth(year) {
        if (!ready() || typeof LunarYear === 'undefined') {
            return 0;
        }
        try {
            return LunarYear.fromYear(Number(year)).getLeapMonth();
        } catch (e) {
            return 0;
        }
    }

    return {
        solarToLunar: solarToLunar,
        lunarToSolar: lunarToSolar,
        leapMonth: leapMonth,
        format: format,
        dayInfo: dayInfo,
        months: MONTHS,
        days: DAYS,
        engine: 'lunar-javascript',
    };
})();
