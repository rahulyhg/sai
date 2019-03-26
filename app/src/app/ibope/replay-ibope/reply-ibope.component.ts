import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { IbopeService } from '../ibope.service';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { UserService } from 'src/app/user/user.service';
import { ResponseApi } from 'src/app/core/response-api';
import { IbopeConfig } from '../ibope-config';

@Component({
    templateUrl: 'reply-ibope.component.html',
    styleUrls: ['reply-ibope.component.scss']
})

export class ReplyIbopeComponent implements OnInit {

    coordinationForm: FormGroup;
    step = 0;
    buttomDisabled: boolean;
    ibopeCoordenationReplyed: any;
    ibopeConfig: IbopeConfig;
    // teacher
    teacherIbope;

    constructor(
        private ibopeService: IbopeService,
        private formBuilder: FormBuilder,
        private alertService: AlertService,
        private userService: UserService) {}

    ngOnInit() {
        this.getIbopeConfig(this.userService.getUserUnit());
        this.buttomDisabled = false;
        this.ibopeCoordenationReplyed = false;

        this.coordinationForm = this.formBuilder.group({
            pedagogicRating:            ['', Validators.required],
            attendantRating:            ['', Validators.required],
            secretaryRating:            ['', Validators.required],
            pedagogicMsg:               [''],
            psychoRating:               ['', Validators.required],
            psychoMsg:                  [''],
            coursewareRating:           ['', Validators.required],
            coursewareMsg:              [''],
            monitoringRating:           ['', Validators.required],
            monitoringMsg:              [''],
            marathonsRating:            ['', Validators.required],
            marathonsMsg:               [''],
            airConditionerRating:       ['', Validators.required],
            chairsRating:               ['', Validators.required],
            cleaningRating:             ['', Validators.required],
            boardRating:                ['', Validators.required],
            bathroomsRating:            ['', Validators.required],
            roomVisualRating:           ['', Validators.required],
            hallRating:                 ['', Validators.required],
            classRoomMsg:               [''],
            // po == Pro Online
            poLessonRating:             ['', Validators.required],
            poPlatformRating:           ['', Validators.required],
            poStudyPlanRating:          ['', Validators.required],
            poExercisesRating:          ['', Validators.required],
            poSimulatesRating:          ['', Validators.required],
            poMsg:                      [''],
            // ro == Redacao Online
            roPlatformRating:           ['', Validators.required],
            roCorrectionRating:         ['', Validators.required],
            roTimeCorrectionRating:     ['', Validators.required],
            roCorrectionComentsRating:  ['', Validators.required],
            roMsg:                      [''],
            // sai
            saiRating:                  ['', Validators.required],
            saiMsg:                     [''],
            socialNetworksRating:       ['', Validators.required],
            classroomScrapsRating:      ['', Validators.required],
            generalComentMsg:           [''],
            userId:                     [this.userService.getUserId()],
            month:                      ['']
        });
    }

    setStep(index: number) {
        this.step = index;
    }

    nextStep() {
        this.step++;
    }

    prevStep() {
        this.step--;
    }

    submit() {

        if (this.coordinationForm.valid && !this.coordinationForm.pending) {

            this.coordinationForm.get('month').setValue(this.ibopeConfig.ibopeMonth);
            this.buttomDisabled = true;
            const newCoordinationIbope = this.coordinationForm.getRawValue();

            this.ibopeService.registerCoordinationIbope(newCoordinationIbope)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {
                        this.alertService.success('Seu ibope foi registrado com sucesso');
                        this.coordinationForm.reset();
                        this.ibopeCoordenationReplyed = true;
                    } else {
                        this.alertService.error(response.error);
                        this.buttomDisabled = false;
                    }

                }, err => {
                    this.alertService.error('Houve um erro ao registrar o IBOPE. Falha na comunicação com a API');
                    this.buttomDisabled = true;
                });
        }
    }

    verifyAvalilableIbopeCoordination() {

        // tslint:disable-next-line:triple-equals
        if (this.ibopeConfig && this.ibopeConfig.ibopeLvl == 2 && !this.ibopeCoordenationReplyed) {
            return true;
        } else {
            return false;
        }
    }


    private getIbopeConfig(unitId: number) {
        this.ibopeService.getIbopeConfig(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.ibopeConfig = response.data as IbopeConfig;
                    this.getCoordinationReplyedStatus(this.userService.getUserId(), this.ibopeConfig.ibopeMonth);

                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as configurações do Ibope. Falha na comunicação com a API'));
    }

    private getCoordinationReplyedStatus(userId: number, month: number) {

        if (this.ibopeConfig.id) {

            this.ibopeService.
                getCoordinationReplyedStatus(userId, month)
                    .subscribe( res => {
                        const response = res.body as ResponseApi;

                        if (!response.error) {
                            this.ibopeCoordenationReplyed = response.data;
                        } else {
                            this.alertService.error(response.error);
                        }
                    },
                    err => this.alertService.error('Houve um erro ao carregar informações sobre o ibope. falha na comunicação com a API'));
        }
    }

}
