import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { TeacherIbope } from './teacher-ibope/teacher-ibope';
import { IbopeConfig } from './ibope-config';

@Injectable({providedIn: 'root'})
export class IbopeService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    registerCoordinationIbope(ibope) {
        return this.http.post(this.API + '/ibope/registerCoordinationIbope', ibope, {observe: 'response'});
    }

    registerTeacherIbope(ibope: TeacherIbope) {
        return this.http.post(this.API + '/ibope/registerTeacherIbope', ibope, {observe: 'response'});
    }

    getStudentTeachersIbope(userClass: number, userId: number, month: number) {
        return this.http.post(this.API + '/ibope/getStudentTeachersIbope', {userClass, userId, month}, {observe: 'response'});
    }

    getIbopeConfig(unitId: number) {
        return this.http.post(this.API + '/ibope/getIbopeConfig', {unitId}, {observe: 'response'});
    }

    getCoordinationReplyedStatus(userId: number, month: number) {
        return this.http.post(this.API + '/ibope/getCoordinationReplyedStatus', {userId, month}, {observe: 'response'});
    }

    getIbopeConfigs(unitId: number) {
        return this.http.post(this.API + '/ibope/getIbopeConfigs', {unitId}, {observe: 'response'});
    }

    updateStatusIbope(active: boolean, ibopeId: number) {
        return this.http.post(this.API + '/ibope/updateStatusIbope', {active, ibopeId}, {observe: 'response'});
    }

    registerIbope(config: IbopeConfig) {
        return this.http.post(this.API + '/ibope/registerIbope', config, {observe: 'response'});
    }
}
