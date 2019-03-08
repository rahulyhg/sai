import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Class } from 'src/app/classes/class';
import { CdkDragDrop, moveItemInArray, transferArrayItem } from '@angular/cdk/drag-drop';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { ClassesService } from 'src/app/classes/classes.service';
import { ResponseApi } from 'src/app/core/response-api';
import { UserService } from 'src/app/user/user.service';
import { Discipline } from '../discipline';
import { MaterialService } from '../material.service';
import { InputFile } from 'ngx-input-file';

@Component({
    templateUrl: './manage-material.component.html',
    styleUrls: ['./manage-material.component.scss']
})

export class ManageMaterialComponent implements OnInit {

    manageMaterialForm: FormGroup;
    disciplines: Discipline[];
    classesOptions: Class[];
    selectedClasses: Class[];

    constructor(
        private materialService: MaterialService,
        private formBuilder: FormBuilder,
        private alertService: AlertService,
        private classesService: ClassesService,
        private userService: UserService) {}

    ngOnInit() {
        this.classesOptions   = [];
        this.selectedClasses  = [];
        this.getClasses(this.userService.getUserUnit());
        this.getDisciplines();
        this.manageMaterialForm = this.formBuilder.group({
            material: ['', Validators.required],
            title: ['', Validators.required],
            discipline: ['', Validators.required],
            classes: [this.selectedClasses]
        });
    }

    drop(event: CdkDragDrop<Class[]>) {

        if (event.previousContainer === event.container) {
          moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
        } else {
          transferArrayItem(event.previousContainer.data,
                            event.container.data,
                            event.previousIndex,
                            event.currentIndex);
        }
    }

    getClasses(unitId: number) {
        this.classesService.getUnitClasses(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.classesOptions = response.data as Class[];
                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as turmas. Falha de comunicação com a API'));
    }

    getDisciplines() {
        this.materialService.getDisciplines()
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.disciplines = response.data as Discipline[];
                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as disciplinas. Falha na comunicação com a API'));
    }

    submit() {
        if (!this.manageMaterialForm.errors && this.selectedClasses.length > 0) {

            this.materialService.saveMaterial(this.generateFormDataMaterial())
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {
                        this.alertService.success('Material cadastrado com sucesso!', 3000);
                        this.manageMaterialForm.reset();
                    } else {
                        this.alertService.error(response.error);
                    }

                }, err => this.alertService.error('Houve um erro ao salvar o material. Falha na comunicação com a API'));

        } else {
            this.alertService.warning('Selecione pelomenos uma turma para vincular o material!');
        }
    }

    private generateFormDataMaterial(): FormData {
        const formData = new FormData();
        const materialArr         = this.manageMaterialForm.get('material').value;
        const title               = this.manageMaterialForm.get('title').value;
        const discipline          = this.manageMaterialForm.get('discipline').value;
        const unitId              = this.userService.getUserUnit();
        const material: InputFile = materialArr[0];
        let classes = '';

        this.selectedClasses.map(c => {
            classes += c.id + ',';
        });

        classes = classes.slice(0, -1);

        formData.append('material', material.file, material.file.name);
        formData.append('title', title);
        formData.append('discipline', discipline);
        formData.append('classes', classes);
        formData.append('unitId', unitId.toString());

        return formData;
    }
}
