import { ref } from 'vue';

const holidays = ref<string[]>([]);
let loaded = false;

export function useHolidays() {
    const loadHolidays = async (year: number = new Date().getFullYear()) => {
        if (loaded) return holidays.value;
        try {
            const resp = await fetch(`https://date.nager.at/api/v3/PublicHolidays/${year}/PT`);
            const data = await resp.json();
            holidays.value = data.map((h: any) => h.date); // formato YYYY-MM-DD
            loaded = true;
        } catch (e) {
            console.error('Erro ao carregar feriados:', e);
        }
        return holidays.value;
    };

    // Função para permitir datas
    const isAllowed = (val: string | Date) => {
        const d = val instanceof Date ? val : new Date(val);
        const iso = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        const day = d.getDay();
        const isWeekend = day === 0 || day === 6;
        const isHoliday = holidays.value.includes(iso);
        return !isWeekend && !isHoliday;
    };

    return { holidays: holidays, loadHolidays, isAllowed };
}
