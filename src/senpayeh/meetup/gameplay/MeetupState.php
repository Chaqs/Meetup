<?php

namespace senpayeh\meetup\gameplay;

interface MeetupState {

    const WAITING = 0;
    const GRACE = 1;
    const PVP = 2;
    const END = 3;

}