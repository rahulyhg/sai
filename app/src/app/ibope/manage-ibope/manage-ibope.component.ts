import { Component, OnInit } from '@angular/core';
import { IbopeService } from '../ibope.service';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ResponseApi } from 'src/app/core/response-api';
import { IbopeConfig } from '../ibope-config';
import { UserService } from 'src/app/user/user.service';
import { MatSlideToggleChange } from '@angular/material';

@Component({
    templateUrl: './manage-ibope.component.html'
})

export class ManageIbopeComponent implements OnInit {

    manageIbopeForm: FormGroup;
    ibopes: IbopeConfig[];

    constructor(
        private ibopeService: IbopeService,
        private alertService: AlertService,
        private formBuilder: FormBuilder,
        private userService: UserService) {}

    ngOnInit(): void {
        this.getIbopeConfigs(this.userService.getUserUnit());

        this.manageIbopeForm = this.formBuilder.group({
            ibopeMonth: ['', Validators.required],
            ibopeLvl: ['', Validators.required],
            unitId: [this.userService.getUserUnit()]
        });
    }

    getIbopeConfigs(unitId: number) {
        this.ibopeService.getIbopeConfigs(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.ibopes = response.data as IbopeConfig[];
                    console.log(this.ibopes);
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao buscar os IBOPES. Falha na comunicação com a API'));
    }

    save() {

        if (!this.manageIbopeForm.errors && !this.manageIbopeForm.pending) {

            const config = this.manageIbopeForm.getRawValue() as IbopeConfig;

            this.ibopeService.registerIbope(config)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {

                        this.manageIbopeForm.reset();
                        this.alertService.success('IBOPE cadastrado!');
                        this.getIbopeConfigs(this.userService.getUserUnit());

                    } else {
                        this.alertService.error(response.error);
                    }
                }, err => this.alertService.error('Houve um erro ao salvar o IBOPE. Falha na comunicação com a API'));
        }
    }

    lastIbopeToggle(event: MatSlideToggleChange, ibopeId: number) {
        this.ibopeService.updateStatusIbope(event.checked, ibopeId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (response.error) {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao atualizar o status do IBOPE. Falha na comunicação com a API'));
    }
}
