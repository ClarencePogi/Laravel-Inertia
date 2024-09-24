<script setup>
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Nav from '../Shared/Nav.vue';
import numeral from 'numeral';
import { ref } from 'vue';

let computation_con = ref('');

const clickBtn = (value) => {
    computation_con.value == 'SYNTAX ERROR' ? computation_con.value = '' : null;
    
    if (computation_con.value == '') {
        ['+', '*', '-', '/'].includes(value) ? '' : computation_con.value += value;
    } else {
        computation_con.value += value;
    }

    console.log(computation_con.value);
}
const compute = () => {
    try {
        computation_con.value = formatCurrency(eval(computation_con.value));
    } catch (error) {
        computation_con.value = 'SYNTAX ERROR';
    }
}
const backspace = () => {
    computation_con.value = computation_con.value.slice(0, -1);
}
const clear = () => {
    computation_con.value = '';
}

function formatCurrency(num) {
  return numeral(num).format('0,0.00');
}

</script>

<template>
    <div class="main-container">
        <div class="row-span-1">
            <Nav />
        </div>
        <div class="row-span-5 grid justify-items-center">
            <div class="calcu-container">
                <div class="row-span-1">
                    <InputText type="text" readonly class="compu" v-model="computation_con" />
                </div>
                <div class="row-span-5 bg-neutral-600 grid grid-cols-3 gap-[1px]">
                    <Button label="CE" @click="clear" severity="danger" />
                    <Button icon="pi pi-delete-left" @click="backspace" severity="contrast" />

                    <Button icon="pi pi-plus" @click="clickBtn('+')" severity="secondary" />
                    <Button icon="pi pi-minus" @click="clickBtn('-')" severity="secondary" />
                    <Button icon="pi pi-percentage" @click="clickBtn('/')" severity="secondary" />
                    <Button icon="pi pi-times" @click="clickBtn('*')" severity="secondary" />

                    <Button label="1" @click="clickBtn('1')" severity="secondary" />
                    <Button label="2" @click="clickBtn('2')" severity="secondary" />
                    <Button label="3" @click="clickBtn('3')" severity="secondary" />
                    <Button label="4" @click="clickBtn('4')" severity="secondary" />
                    <Button label="5" @click="clickBtn('5')" severity="secondary" />
                    <Button label="6" @click="clickBtn('6')" severity="secondary" />
                    <Button label="7" @click="clickBtn('7')" severity="secondary" />
                    <Button label="8" @click="clickBtn('8')" severity="secondary" />
                    <Button label="9" @click="clickBtn('9')" severity="secondary" />
                    <Button label="0" @click="clickBtn('0')" severity="secondary" />
                    <Button label="." @click="clickBtn('.')" severity="secondary" />
                    <Button label="=" @click="compute" severity="success" />

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.main-container {
    @apply bg-slate-800 h-screen grid grid-rows-6;
}

.calcu-container {
    @apply w-[25%] h-[90%] bg-slate-100 grid grid-rows-6;
}

.compu {
    @apply w-full h-full text-4xl bg-stone-950 text-right text-white;
}

button {
    @apply !rounded-none w-full font-bold text-xl;
}
</style>
