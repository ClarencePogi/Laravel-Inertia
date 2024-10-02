<script setup>
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import FloatLabel from 'primevue/floatlabel';
import Textarea from 'primevue/textarea';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Toast from 'primevue/toast';
import ConfirmPopup from 'primevue/confirmpopup';
import DangerButton from '@/Components/DangerButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { FilterMatchMode } from '@primevue/core/api';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Select from 'primevue/select';
import Checkbox from '@/Components/Checkbox.vue';
import Skeleton from 'primevue/skeleton';

const confirm = useConfirm();
const toast = useToast();

let errors = ref({});

let isEdit = ref(false);
let isAssignTask = ref(false);
let isFlipped = ref(false);
let visible = ref(false);
const loading = ref(true);


const form = useForm({
    id: '',
    title: '',
    description: '',
    due_date: '',
    status: ''
});

const assignTask = useForm({
    selectedEmployee: '',
    isChecked: []
})

const props = defineProps({
    tasks: Array,
    permissions: Array,
    employees: Array
});

function getCheck(ev) {
    const task = ev.target.value;

    if(assignTask.isChecked.includes(task)) {
        assignTask.isChecked = assignTask.isChecked.filter(val => val != task);
        console.log(assignTask.isChecked);

    } else {
        ev.target.checked ? assignTask.isChecked.push(ev.target.value) : null;
        console.log(assignTask);
    }
}

watch(visible, (newValue, oldValue) => {
    if (!newValue) {
        errors.value = '';
    }
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    description: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    due_date: { value: null, matchMode: FilterMatchMode.IN },
    status: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const statusList = ref([
    { name: 'Complete' },
    { name: 'Incomplete' }
]);


const showSuccess = (message, status) => {
    toast.add({ severity: status, summary: message, detail: status, life: 3000 });
};

const submitForm = () => {
    form.post(route('task.store'), {
        onSuccess: (res) => {
            console.log(res);
            errors.value = '';
            form.reset();
            showSuccess('Successfully add task', 'success');

        },
        onError: (error) => {
            errors.value = error

        }
    });
}

const confirmDelete = (event, id) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Do you want to delete this record?',
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            console.log(id);
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 });
            router.delete(route('task.delete', id));
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
};

function edit(obj) {
    visible.value = true;
    isEdit.value = true;

    Object.entries(obj).forEach(task => {
        form[task[0]] = task[0] == 'status' ? (task[1] == 0 ? 'Incomplete' : 'Complete') : task[1];
    });

    console.log(form);
}

function cancelEdit() {
    isEdit.value = false;
    visible.value = false;
    form.reset();
}

function update() {
    form.put(route('task.update'), {
        onSuccess: () => {
            errors.value = '';
            form.reset();
            showSuccess('Successfully update task', 'success');
        },
        onError: (error) => {
            errors.value = error

        }

    })
}

function can(permission) {
    return props.permissions.includes(permission);
}

function loadContent() {
    setTimeout(() => {
        loading.value = false;
    }, 1000);
}

onMounted(() => {
    loadContent();
});

</script>

<template>

    <Head title="Task management" />
    <Toast />

    <AuthenticatedLayout :permissions="props.permissions">
        <div class="grid bg-slate-50 w-full grid-cols-1 h-[500px]">
            <div class="grid grid-rows-6 p-5">
                <div class="row-span-1 grid justify-items-end items-end">
                    <div class="flex gap-2 mb-2">
                        <Select v-model="assignTask.selectedEmployee" v-if="isAssignTask" :options="props.employees"
                            optionLabel="name" optionValue="id" placeholder="Select a employee" checkmark :highlightOnSelect="false"
                            class="w-full md:w-56 !absolute left-2" />
                        <PrimaryButton v-if="assignTask.isChecked.length > 0" class="!absolute left-[17%] mt-1"
                            @click="assignTask.post(route('assign.task'))">Assign
                        </PrimaryButton>

                        <PrimaryButton v-show="!loading" v-if="can('create task')"
                            @click="visible = true; form.reset(); isEdit = false">Add task</PrimaryButton>
                        <PrimaryButton v-if="!loading" v-show="!isAssignTask"
                            @click="isAssignTask = true">Assign task</PrimaryButton>
                        <DangerButton v-if="isAssignTask" @click="isAssignTask = false">Cancel</DangerButton>
                        <Skeleton v-if="loading" width="8rem" height="3rem"></Skeleton>
                        <Skeleton v-if="loading" width="8rem" height="3rem"></Skeleton>


                    </div>
                </div>
                <Skeleton v-if="loading" width="100%" height="100%" class="row-span-5"></Skeleton>
                <DataTable v-if="!loading" v-model:filters="filters" dataKey="id" filterDisplay="row"
                    :value="props.tasks" paginator :rows="6"
                    :globalFilterFields="['title', 'description', 'due_date', 'status']" tableStyle="min-width: 50rem"
                    class="row-span-5 w-full" :class="{ 'animate-flip': isFlipped }">

                    <template #header>
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon class="pi pi-search" />
                                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </template>
                    <template #empty> No customers found. </template>
                    <Column header="Action" v-if="isAssignTask">
                        <template #body="slotProps">
                            <Checkbox :value="slotProps.data.title"
                                @change="getCheck" :checked="false"></Checkbox>
                        </template>
                    </Column>
                    <Column field="title" header="Title">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column field="description" header="Description">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column field="due_date" header="Due date">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="slotProps">
                            <p
                                :class="{ 'text-red-500': slotProps.data.status === 0, 'text-blue-500': slotProps.data.status === 1 }">
                                {{ slotProps.data.status === 0 ?
                                    'Incomplete' : 'Complete' }}</p>
                        </template>
                    </Column>
                    <Column header="Action">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <PrimaryButton type="button" v-if="can('update task')" @click="edit(slotProps.data)">
                                    Edit</PrimaryButton>
                                <DangerButton @click="confirmDelete($event, slotProps.data.id)"
                                    v-if="can('delete task')" type="button">Delete
                                </DangerButton>
                                <ConfirmPopup></ConfirmPopup>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="isEdit ? 'Edit Task' : 'Create Task'" :style="{ width: '40rem' }">
        <span v-if="!isEdit" class="text-surface-500 dark:text-surface-400 block mb-8">Enter task information.</span>
        <span v-if="isEdit" class="text-surface-500 dark:text-surface-400 block mb-8">Update task information.</span>


        <form @submit.prevent="submitForm" class="grid grid-cols-2 h-full gap-10">
            <div class="card flex flex-col relative">
                <FloatLabel>
                    <InputText id="id" type="hidden" v-model="form.id" />

                    <InputText id="title" class="w-full" v-model="form.title" />
                    <label for="title">Title</label>
                </FloatLabel>
                <span v-if="errors?.title" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.title }}</span>
            </div>
            <div class="card flex justify-center flex-col relative">
                <FloatLabel>
                    <DatePicker v-model="form.due_date" :format="'YYYY-MM-DD'" class="w-full" :only-date="true" />
                    <label>Due date</label>
                </FloatLabel>
                <span v-if="errors?.due_date" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.due_date }}</span>
            </div>
            <div class="card flex flex-col relative justify-center">
                <FloatLabel>
                    <Textarea v-model="form.description" rows="3" class="w-full" />
                    <label>Description</label>
                </FloatLabel>
                <span v-if="errors?.description" class="absolute bottom-[-20px] text-sm text-red-600"
                    severity="error">{{
                        errors?.description }}
                </span>
            </div>

            <div class="card flex justify-center flex-col relative">
                <Select v-model="form.status" :options="statusList" optionLabel="name" optionValue="name"
                    placeholder="Select Status" class="w-full" />

                <span v-if="errors?.status" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.status }}</span>
            </div>


            <PrimaryButton v-if="!isEdit" :disabled="form.processing"
                class="col-span-2 max-w-28 text-center justify-self-center" type="submit">Submit</PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="update" type="button">Update</PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="cancelEdit" type="button">Cancel update
            </PrimaryButton>
        </form>
    </Dialog>
</template>

<style scoped>
.animate-flip {
    animation: flip 0.6s ease-out forwards;
}

.animate-flip-back {
    animation: flip-back 0.6s ease-out forwards;
}
</style>