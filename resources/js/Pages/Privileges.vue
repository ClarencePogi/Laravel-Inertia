<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmPopup from 'primevue/confirmpopup';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
import Toast from 'primevue/toast';


const props = defineProps({
    rolesWithPermssions: Array,
    permisions: Array,
    userPermissions: Array
});

let visible = ref(false);
const confirm = useConfirm();
const toast = useToast();
let isEdit = ref(false);
let errors = ref({});




const form = useForm({
    id: '',
    role: '',
    permissions: {}
})

watch(visible, (newVal, oldVal) => {
    if (!newVal) {
        form.reset();
        isEdit.value = false;
        errors.value = '';
    }
});

function deleteRole(event, id) {
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
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 });
            router.delete(route('privileges.manage.delete', id));
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });


}

const submitForm = () => {
    form.post(route('privileges.manage.store'), {
        onSuccess: () => {
            errors.value = '';
            form.reset();
            showSuccess('Successfully Add Role', 'success');
        },
        onError: (error) => {
            errors.value = error;
        }
    });
}

function edit(data) {
    form.id = data.id;
    form.role = data.name;
    form.type = data.guard_name;

    isEdit.value = true;
    visible.value = true;
    // if(data.permissions) {
    data.permissions.forEach(permission => {
        const per = permission.name.split(' ').join('_');
        form.permissions[per] = true;

    });

    console.log(form.permissions);
}

const showSuccess = (message, status) => {
    toast.add({ severity: status, summary: message, detail: status, life: 3000 });
};

function update() {
    form.put(route('privileges.manage.update'), {
        onSuccess: () => {
            errors.value = '';
            form.reset();
            showSuccess('Successfully Update Role', 'success');
            visible.value = false;
        },
        onError: (error) => {
            errors.value = error;
        }
    });
}

function watching(prop) {
    if(!form.permissions[prop]) {
        delete form.permissions[prop];
    } else {
        form.permissions[prop];
    }

}

function can(permission) {
    return props.userPermissions.includes(permission);
}

</script>

<template>
    <Toast></Toast>
    <AuthenticatedLayout :permissions="props.userPermissions">
        <div class="grid w-full grid-cols-1 h-screen">
            <div class="grid grid-rows-6 p-5">
                <div class="row-span-1 flex items-center justify-center relative">
                    <h1 class="text-3xl font-bold">Privileges</h1>
                    <Button label="Add role" v-if="can('create privileges')" class="!absolute right-3 " @click="visible = true" />
                </div>
                <div class="row-span-5 grid justify-items-center">
                    <DataTable :value="props.rolesWithPermssions" tableStyle="min-width: 50rem" class="fit-content">
                        <Column field="name" header="Fullname"></Column>
                        <Column header="Permissions">
                            <template #body="slotProps">
                                <ul class="grid grid-cols-2 justify-items-center">
                                    <li v-for="permission of slotProps.data.permissions">{{ permission.name }}</li>
                                </ul>
                            </template>
                        </Column>
                        <Column header="Action">
                            <template #body="slotProps">
                                <div class="flex gap-2">
                                    <PrimaryButton @click="edit(slotProps.data)">Edit</PrimaryButton>
                                    <DangerButton @click="deleteRole($event, slotProps.data.id)" v-if="can('delete privileges')">Delete</DangerButton>
                                    <ConfirmPopup></ConfirmPopup>

                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <Dialog v-model:visible="visible" modal :header="!isEdit ? 'Add privileges' : 'Update privileges'"
        :style="{ width: '30vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-2">
            <div class="relative">
                <FloatLabel>
                    <InputText id="username" v-model="form.role" class="w-full" />
                    <label for="username">Role Name</label>
                </FloatLabel>
                <span v-if="errors?.role" class="absolute bottom-[-20px] text-sm text-red-600" severity="error">{{
                    errors?.role }}
                </span>
            </div>

            <ul class="col-span-2 m-3 grid grid-cols-2 gap-3 mt-5 justify-items-center">
                <li v-for="(permission, i) of permisions" class="flex gap-5 items-center">
                    <label :for="permission.guard_name + '_' + i">{{ permission.name }}</label>
                    <Checkbox :id="permission.guard_name + '_' + i" @change="watching(permission.name.split(' ').join('_'))"
                        v-model="form.permissions[permission.name.split(' ').join('_')]"
                        :checked="form.permissions[permission.name.split(' ').join('_')] ? true : false" />

                </li>
            </ul>

            <PrimaryButton v-if="!isEdit" :disabled="form.processing"
                class="col-span-2 max-w-28 text-center justify-self-center" type="submit">Submit</PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="update" type="button">Update</PrimaryButton>
            <PrimaryButton v-if="isEdit" class="text-center" @click="visible = false" type="button">Cancel update
            </PrimaryButton>
        </form>
    </Dialog>

</template>