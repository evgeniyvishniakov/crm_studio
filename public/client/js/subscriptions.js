// ===== ФУНКЦИИ ДЛЯ СТРАНИЦЫ ПОДПИСОК =====

function renewSubscription() {
    if (confirm(confirmRenewSubscriptionMessage)) {
        window.location.href = renewSubscriptionUrl;
    }
}

function changePlan() {
    window.location.href = changePlanUrl;
}

function selectPlan(planId) {
    if (confirm(confirmChangePlanMessage)) {
        // Используем общую функцию для создания и отправки формы
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = changePlanUrl;
        
        // Добавляем данные плана
        const planInput = document.createElement('input');
        planInput.type = 'hidden';
        planInput.name = 'plan_id';
        planInput.value = planId;
        
        // Добавляем CSRF токен
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfTokenValue;
        
        form.appendChild(planInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelAutoRenewal() {
    if (confirm(confirmCancelAutoRenewalMessage)) {
        window.location.href = cancelSubscriptionUrl;
    }
} 