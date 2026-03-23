import { ref } from 'vue';
import supplierOrderStatusesRoutes from '@/routes/supplier-order-statuses';

type UseStatusDrawerOptions = {
    emit: (event: 'update:open', value: boolean) => void,
};

export const useStatusDrawer = ({ emit }: UseStatusDrawerOptions) => {
    const editingStatusId = ref<number | null>(null);

    const formOptions = {
        preserveScroll: true,
        preserveState: true,
    };

    const openDrawer = (): void => {
        emit('update:open', true);
    };

    const closeDrawer = (): void => {
        emit('update:open', false);
    };

    const syncDrawerOpenState = (value: boolean): void => {
        emit('update:open', value);
    };

    const startEditing = (statusId: number): void => {
        editingStatusId.value = statusId;
    };

    const stopEditing = (): void => {
        editingStatusId.value = null;
    };

    const handleCreateSuccess = (): void => {
        openDrawer();
    };

    const handleUpdateSuccess = (): void => {
        stopEditing();
        openDrawer();
    };

    const createStatusForm = () => {
        return supplierOrderStatusesRoutes.store.form();
    };

    const updateStatusForm = (statusId: number) => {
        return supplierOrderStatusesRoutes.update.form(statusId);
    };

    return {
        createStatusForm,
        editingStatusId,
        formOptions,
        handleCreateSuccess,
        handleUpdateSuccess,
        closeDrawer,
        openDrawer,
        startEditing,
        stopEditing,
        syncDrawerOpenState,
        updateStatusForm,
    };
};
