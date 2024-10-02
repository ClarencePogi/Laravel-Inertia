<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { ref, watch } from 'vue';
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import Password from 'primevue/password';
import { router, useForm } from '@inertiajs/vue3';
import Select from 'primevue/select';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import ConfirmPopup from 'primevue/confirmpopup';



const props = defineProps({
    employees: Array,
    permissions: Array,
    roles: Array
});

let isEdit = ref(false);
let visible = ref(false);
let errors = ref({});
const toast = useToast();
const confirm = useConfirm();

const form = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: '',
    role: ''
});

watch(visible, (newValue, oldValue) => {
    if (!newValue) {
        errors.value = '';
        isEdit.value = false;
    }
});


const statusList = ref([
    { name: 'Active' },
    { name: 'Inactive' }
]);

const showSuccess = (message, status) => {
    toast.add({ severity: status, summary: message, detail: status, life: 3000 });
};


const submitForm = () => {
    form.post(route('users.manage.store'), {
        onSuccess: () => {
            errors.value = '';
            form.reset();
            showSuccess('Successfully Add Employee', 'success');
        },
        onError: (error) => {
            errors.value = error;
        }
    })
};

function update(event) {
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
            label: 'Update',
            severity: 'info'
        },
        accept: () => {
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 });
            form.put(route('users.manage.update'), {
                onSuccess: () => {
                    errors.value = '';
                    form.reset();
                    showSuccess('Successfully Updated Employee', 'success');
                    visible.value = false;
                },
                onError: (error) => {
                    errors.value = error;
                }
            });
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
}


function edit(data, role) {
    visible.value = true;
    isEdit.value = true;

    Object.entries(data).forEach(obj => {
        form[obj[0]] = obj[0] == 'status' ? obj[1] ? 'Active' : 'Inactive' : obj[1];
        console.log(obj[1]);
    });

    form.role = role;

}

function cancelEdit() {
    isEdit.value = false;
    visible.value = false;
    form.reset();
}

function can(permission) {
    return props.permissions.includes(permission);
}

function obscuredText(text) {
    const lengthToHide = Math.min(5, text.length);

    const hiddenPart = '*'.repeat(lengthToHide);

    return hiddenPart + text.slice(lengthToHide);
}
</script>

<template>
    <AuthenticatedLayout :permissions="props.permissions">
        <div class="grid w-full grid-cols-1 h-screen">
            <div class="grid grid-rows-6 p-5">
                <div class="row-span-1 relative flex justify-center items-center">
                    <h1 class="text-3xl font-bold">User Management</h1>
                    <PrimaryButton @click="visible = true" v-if="can('create user')" class="!absolute bottom-2 right-2">
                        Add user</PrimaryButton>
                </div>
                <div class="row-span-5 grid justify-items-center">
                    <DataTable :value="employees" tableStyle="min-width: 50rem" class=" w-[80%]">
                        <Column field="name" header="Fullname"></Column>
                        <Column header="Email">
                            <template #body="slotProps">
                               <p>{{ obscuredText(slotProps.data.email) }}</p>
                            </template>
                        </Column>
                        <Column header="Status">
                            <template #body="slotProps">
                                <p
                                    :class="{ 'text-green-500': slotProps.data.status, 'text-gray-500': !slotProps.data.status }">
                                    {{ slotProps.data.status ? 'Active' : 'Inactive' }}</p>
                            </template>
                        </Column>
                        <Column header="Role">
                            <template #body="slotProps">
                                <p>{{ slotProps.data.roles[0].name }}</p>
                            </template>
                        </Column>
                        <Column header="Action" v-if="can('update user')">
                            <template #body="slotProps">
                                <div class="flex gap-2">
                                    <PrimaryButton @click="edit(slotProps.data, slotProps.data.roles[0].name)">Edit
                                    </PrimaryButton>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="isEdit ? 'Edit Employee' : 'Create Employee'"
        :style="{ width: '40rem' }">
        <span v-if="!isEdit" class="text-surface-500 dark:text-surface-400 block mb-8">Enter employee
            information.</span>
        <span v-if="isEdit" class="text-surface-500 dark:text-surface-400 block mb-8">Update employee
            information.</span>

        <form @submit.prevent="submitForm" class="grid grid-cols-2 h-full gap-10">
            <div class="card flex flex-col relative">
                <FloatLabel>
                    <InputText id="id" type="hidden" v-model="form.id" />

                    <InputText id="name" class="w-full" v-model="form.name" />
                    <label for="name">Fullname</label>
                </FloatLabel>
                <span v-if="errors?.name" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.name }}</span>
            </div>
            <div class="card flex justify-center flex-col relative">
                <FloatLabel>
                    <InputText id="email" class="w-full" v-model="form.email" />
                    <label>Email</label>
                </FloatLabel>
                <span v-if="errors?.email" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.email }}</span>
            </div>
            <div class="card flex flex-col relative">
                <Select v-model="form.status" :options="statusList" optionLabel="name" optionValue="name"
                    placeholder="Select Status" class="w-full" />

                <span v-if="errors?.status" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.status }}</span>
            </div>
            <div class="card flex flex-col relative">
                <Select v-model="form.role" :options="props.roles" optionLabel="name" optionValue="name"
                    placeholder="Select role" class="w-full" />

                <span v-if="errors?.status" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.status }}</span>
            </div>
            <div class="card flex flex-col relative justify-center" v-if="!isEdit">
                <FloatLabel>
                    <Password id="password" class="w-[17rem]" v-model="form.password" toggleMask />
                    <label>Password</label>
                </FloatLabel>
                <span v-if="errors?.password" class="absolute bottom-[-45px] text-sm text-red-600" severity="error">{{
                    errors?.password }}
                </span>
            </div>

            <div class="card grid flex-col relative justify-items-center" v-if="!isEdit">
                <FloatLabel>
                    <Password id="password_confirmation" class="w-[17rem]" toggleMask
                        v-model="form.password_confirmation" />
                    <label>Confirm Password</label>
                </FloatLabel>
                <span v-if="errors?.password" class="absolute bottom-[-45px] text-sm text-red-600" severity="error">{{
                    errors?.password }}
                </span>
            </div>





            <PrimaryButton v-if="!isEdit" :disabled="form.processing"
                class="col-span-2 max-w-28 text-center justify-self-center" type="submit">Submit</PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="update($event)" type="button">Update
            </PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="cancelEdit" type="button">Cancel update
            </PrimaryButton>
            <ConfirmPopup></ConfirmPopup>
        </form>
    </Dialog>
</template>