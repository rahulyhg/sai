import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { UserService } from 'src/app/user/user.service';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';
import { ResponseApi } from '../response-api';
import { SystemVersion } from './systemVersion';
import { AlertService } from 'src/app/shared/services/alert/alert.service';

@Injectable({ providedIn: 'root' })

export class AuthGuard implements CanActivate {

    constructor(
        private userService: UserService,
        private router: Router,
        private authService: AuthService,
        private alertService: AlertService) {}

    canActivate(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot): boolean | Observable<boolean> | Promise<boolean> {

        if (!this.userService.isLogged()) {
            this.router.navigate(['signin']);
            return false;
        }

        this.authService.isUpdated()
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    const currentBuild = response.data as SystemVersion;
                    const localBuild   = this.authService.getCurrentVersionLoged();

                    if (!(currentBuild.version === localBuild.version)) {
                        this.userService.logout();
                    }

                } else {
                    this.alertService.error('Não foi possível verificar as atualizações do sistema');
                }
            }, err => this.alertService.error('Não foi possível verificar as atualizações do sistema'));

        return true;
    }
}
