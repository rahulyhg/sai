import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { UserService } from 'src/app/user/user.service';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })

export class AdmGuard implements CanActivate {

    constructor(
        private userService: UserService) {}

    canActivate(
        route: ActivatedRouteSnapshot,
        state: RouterStateSnapshot): boolean | Observable<boolean> | Promise<boolean> {

        if (!this.userService.isAdm()) {
            return false;
        }
        return true;
    }
}
