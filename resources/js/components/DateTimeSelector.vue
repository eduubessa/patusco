<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue';
import { useHolidays } from '@/utils/holidays';

const props = defineProps({
    date: [String, null],
    period: [String, null],
    hour: [String, null],
    validation: String
});

const emit = defineEmits(['update:date', 'update:period', 'update:hour']);

const { loadHolidays, isAllowed } = useHolidays();

const menu = ref(false);

function toIsoDate(val: string | Date) {
    if (!val) return '';

    if (val instanceof Date) {
        const d = val;
        if (isNaN(d.getTime())) return '';
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    const s = val.trim();
    const parts = s.split('-');

    if (parts.length === 3) {
        if (parts[0].length === 4) {
            const [y, m, d] = parts;
            const dObj = new Date(`${y}-${m}-${d}`);
            if (!isNaN(dObj.getTime())) return `${y}-${m}-${d}`;
        } else {
            // dd-mm-yyyy
            const [d, m, y] = parts;
            const dObj = new Date(`${y}-${m}-${d}`);
            if (!isNaN(dObj.getTime())) return `${y}-${m}-${d}`;
        }
    }

    const parsed = new Date(s);
    if (isNaN(parsed.getTime())) return '';
    return toIsoDate(parsed);
}

const pickerDate = ref(props.date ? toIsoDate(props.date) : '');
const displayDate = computed({
    get() {
        if (!pickerDate.value) return '';
        const iso = typeof pickerDate.value === 'string' ? pickerDate.value : toIsoDate(pickerDate.value);
        const parts = iso.split('-');
        if (parts.length !== 3) return '';
        const [year, month, day] = parts;
        return `${day}-${month}-${year}`;
    },
    set(val: string) {
        const parts = val.split('-');
        if (parts.length === 3) {
            const [day, month, year] = parts;
            pickerDate.value = `${year}-${month}-${day}`;
        } else {
            pickerDate.value = '';
        }
    },
});

// Emitir a data no formato de exibição (`dd-mm-yyyy`)
watch(displayDate, val => emit('update:date', val));

const periodOptions = ['Manhã', 'Tarde'];
const hourOptions = ref<string[]>([]);

const selectedPeriod = ref(props.period);
const selectedHour = ref(props.hour);
const datetimeFinal = ref(null);

watch(selectedPeriod, val => {
    if (val === 'Manhã') hourOptions.value = ['08:00', '09:00', '10:00', '11:00'];
    else if (val === 'Tarde') hourOptions.value = ['14:00', '15:00', '16:00', '17:00'];
    else hourOptions.value = [];
    selectedHour.value = null;
    emit('update:period', val);
    emit('update:hour', null);
});

watch(selectedHour, val => {
    console.log('Selected hour changed:', val);
    console.log('Display date:', displayDate.value);

    datetimeFinal.value = `${displayDate.value} ${val}`;

    emit('update:hour', val);
});

onMounted(() => {
    loadHolidays();
    if (displayDate.value) emit('update:date', displayDate.value);
});
</script>

<template>
    <div class="form-row">
        <label class="form-label">Disponibilidade</label>
        <div class="form-input">
            <v-menu
                v-model="menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
            >
                <template #activator="{ props: activatorProps }">
                    <v-text-field
                        v-bind="activatorProps"
                        v-model="displayDate"
                        placeholder="Escolha a data"
                        dense
                        variant="outlined"
                        hide-details="auto"
                        readonly
                        style="flex:1"
                        :error="props.validation != null && props.validation.length > 0"
                        :error-messages="props.validation"
                    />
                </template>
                <v-date-picker
                    v-model="pickerDate"
                    :allowed-dates="isAllowed"
                    @update:model-value="val => { pickerDate.value = val; menu=false }"
                />
            </v-menu>

            <v-select
                v-model="selectedPeriod"
                :items="periodOptions"
                dense
                variant="outlined"
                hide-details
                style="flex:1"
                :disabled="!pickerDate"
                :error="props.validation != null && props.validation.length > 0"
            />

            <v-select
                v-model="selectedHour"
                :items="hourOptions"
                dense
                variant="outlined"
                hide-details
                style="flex:1"
                :disabled="!selectedPeriod"
                :error="props.validation != null && props.validation.length > 0"
            />
        </div>
    </div>
</template>

<style scoped>
.form-row {
    display: flex;
    align-items: center;
    gap: 75px;
    margin-bottom: 20px;
}
.form-label {
    width: 120px;
    font-weight: 600;
    color: #333;
}
.form-input {
    display: flex;
    flex: 1;
    gap: 8px;
    align-items: center;
}
</style>
