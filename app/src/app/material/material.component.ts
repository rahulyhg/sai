import { Component, OnInit } from '@angular/core';
import { Discipline } from './discipline';
import { MaterialService } from './material.service';
import { ResponseApi } from '../core/response-api';
import { AlertService } from '../shared/services/alert/alert.service';
import { Material } from './material';
import { UserService } from '../user/user.service';
import { environment } from 'src/environments/environment';

@Component({
    templateUrl: './material.component.html'
})

export class MaterialComponent implements OnInit {

    disciplines: Discipline[];
    materials: Material[];
    private API = environment.ApiUrl;

    constructor(
        private materialService: MaterialService,
        private alertService: AlertService,
        private userService: UserService) {}

    ngOnInit() {
        this.getDisciplines();
        this.getMaterial(this.userService.getUserUnit(), this.userService.getUserId());
    }

    private getDisciplines() {
        this.materialService.getDisciplines()
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.disciplines = response.data as Discipline[];
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao carregar as disciplinas. Falha na comunicação com a API'));
    }

    getMaterial(unitId, userId) {

        this.materialService.getMaterial(unitId, userId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.materials = response.data as Material[];
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao buscar os materiais. Falha na comunicação com a API'));
    }

    isMonitor(): boolean {

        // tslint:disable-next-line:triple-equals
        return (this.userService.getProfileId() == 3 || this.userService.getProfileId() == 4);
    }

    openMaterial(path) {
        window.open(this.API + '/' + path, '_blank');
    }

    delete(materialId: number, index: number) {
        this.materialService.deleteMaterial(materialId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (response.error) {
                    this.alertService.error(response.error);
                } else {
                    this.materials.splice(index, 1);
                    this.alertService.success('Material removido com sucesso.');
                }
            }, err => this.alertService.error('Houve um erro ao remover o material. Falha na comunicação com a API'));
    }
}
