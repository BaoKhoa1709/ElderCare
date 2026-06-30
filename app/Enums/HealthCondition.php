<?php

namespace App\Enums;

enum HealthCondition: string
{
    case SUY_GIAM_TRI_NHO = 'SUY_GIAM_TRI_NHO';
    case PARKINSON = 'PARKINSON';
    case DOT_QUY = 'DOT_QUY';
    case BENH_TIM_MACH = 'BENH_TIM_MACH';
    case BENH_TIEU_DUONG = 'BENH_TIEU_DUONG';
    case BENH_XUONG_KHOP = 'BENH_XUONG_KHOP';
    case DI_KHO = 'DI_KHO';
    case THI_LUC_YEU = 'THI_LUC_YEU';
    case THINH_LUC_KEM = 'THINH_LUC_KEM';
    case TAM_THAN = 'TAM_THAN';
    case UNG_THU = 'UNG_THU';
    case SAU_PHAU_THUAT = 'SAU_PHAU_THUAT';
    case KHO_THO = 'KHO_THO';
    case BEDRIDDEN = 'BEDRIDDEN';
}
