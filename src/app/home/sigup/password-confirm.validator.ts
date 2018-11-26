import { ValidatorFn, FormGroup } from '@angular/forms';

export const passwordConfirmValidator: ValidatorFn = (formGroup: FormGroup) => {

    const password = formGroup.get('password').value;
    const passwordconfirm = formGroup.get('passwordConfirm').value;

    return password === passwordconfirm
        ? null
        : { passwordConfirmValidator: true };
};
