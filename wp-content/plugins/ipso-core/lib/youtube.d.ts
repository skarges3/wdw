declare module YouTube{

    interface Response {
        kind:string;
        etag:string;
        nextPageToken:string;
        prevPageToken:string;
        pageInfo: PageInfo;
        items:Resource[];
    }

    interface PageInfo{
        totalResults:number;
        resultsPerPage:number;
    }

    interface Resource{
        kind:string;
        etag:string;
        id:string;
        snippet:Snippet;
    }

    interface Snippet{
        publishedAt: Date;
        channelId:string;
        title:string;
        description:string;
        thumbnails:Thumbnails;
        channelTitle:string;
        tags:string[];
        categoryId:string;
        contentDetails:ContentDetails;
        status:Status;
        statistics:Statistics;
        player:Player;
        topicDetails:TopicDetails;
        recordingDetails:RecordingDetails;
        fileDetails:FileDetails;
        processingDetails:ProcessingDetails;
        suggestions:Suggestions;
    }

    interface Suggestions{
        processingErrors:ProcessingError[];
        processingWarnings:ProcessingWarning[];
        processingHints:ProcessingHint[];
        tagSuggesions:TagSuggesion[];
        editorSuggestions:EditorSuggestion[];
    }

    enum EditorSuggestion{
        audioQuietAudioSwap,
        videoAutoLevels,
        videoCrop,
        videoStabilize
    }

    interface TagSuggesion{
        tag:string;
        categoryRestricts:string[];
    }

    enum ProcessingError{
        archiveFile,
        audioFile,
        docFile,
        imageFile,
        notAVideoFile,
        projectFile
    }

    enum ProcessingWarning{
        hasEditlist,
        inconsistentResolution,
        problematicAudioCodec,
        problematicVideoCodec,
        unknownAudioCodec,
        unknownContainer,
        unknownVideoCodec
    }

    enum ProcessingHint{
        nonStreamableMov,
        sendBestQualityVideo
    }

    interface ProcessingDetails{
        processingStatus: ProcessingStatus;
        processingProgress:ProcessingProgress;
        fileDetailsAvailability:string;
        processingIssuesAvailability:string;
        tagSuggestionsAvailability:string;
        editorSuggestionsAvailability:string;
        thumbnailsAvailability:string;
    }

    interface ProcessingProgress{
        processingFailureReason: ProcessingFailureReason;
    }

    enum ProcessingFailureReason{
        other,
        streamingFailed,
        transcodeFailed,
        uploadFailed
    }

    enum ProcessingStatus{
        failed,
        processing,
        succeeded,
        terminated
    }

    interface FileDetails{
        fileName:string;
        fileSize: number;
        fileType:FileType;
        container:string;
        videoStreams:VideoStream[];
        audioStream:AudioStream[];
        durationMs:number;
        bitrateBps:number;
        recordingLocation:Location;
        creationTime:string
    }

    interface AudioStream{
        channelCount:number;
        codec:string;
        bitrateBps:number;
        vendor:string;
    }

    interface VideoStream{
        widthPixels:number;
        heightPixels:number;
        frameRateFps:number;
        aspectRatio:number;
        codec:string;
        bitrateBps:number;
        rotation:Rotation;
        vendor:string;
    }

    enum Rotation{
        clockwise,
        counterClockwise,
        none,
        other,
        upsideDown
    }

    enum FileType{
        archive,
        audio,
        document,
        image,
        other,
        project,
        video
    }

    interface RecordingDetails{
        locationDescription:string;
        location:Location;
        recordingDate:number;
    }

    interface Location{
        latitude:number;
        longitude:number;
        altitude:number;
    }

    interface TopicDetails{
        topicIds:string[];
        relevantTopicIds:string[];
    }

    interface Player{
        embedHtml:string;
    }

    interface Statistics{
        viewCount:number;
        likeCount:number;
        dislikeCount:number;
        favoriteCount:number;
        commentCount:number;
    }

    interface Status{
        uploadStatus:UploadStatus;
        failureReason:FailureReason;
        rejectionReason:RejectionReason;
        privacyStatus:PrivacyStatus;
        license:License;
        embeddable:boolean;
        publicStatsViewable:boolean;
    }

    enum UploadStatus{
        deleted,
        failed,
        processed,
        rejected,
        uploaded
    }

    enum FailureReason{
        codec,
        conversion,
        emptyFile,
        invalidFile,
        tooSmall,
        uploadAborted
    }

    enum RejectionReason{
        claim,
        copyright,
        duplicate,
        inappropriate,
        length,
        termsOfUse,
        trademark,
        uploaderAccountClosed,
        uploaderAccountSuspended
    }

    enum PrivacyStatus{
        private,
        public,
        unlisted
    }

    enum License{
        creativeCommon,
        youtube
    }

    interface Thumbnails{
        default: Thumbnail;
        medium:Thumbnail;
        high:Thumbnail;
    }

    interface Thumbnail{
        url: string;
        width:number;
        height:number;
    }

    interface ContentDetails{
        duration:string;
        dimension:string;
        definition:string;
        caption:string;
        licensedContent:string;
        regionRestriction:RegionRestriction;
        contentRating:ContentRating;
    }

    interface RegionRestriction{
        allowed:string[];
        blocked:string[];
    }

    interface ContentRating{
        mpaarating: MPAARating;
        tvpgRating:TVPGRating;
        bbfcRating: BBFCRating;
        chvrsRating:CHVRSRating;
        eirinRating:EIRINRating;
        cbfcRating:CBCFCRating;
        fmocRating:FMOCRating;
        icaaRating:ICAARating;
        acbRating:ACBRating;
        oflcRating:OFLCRating;
        fskRating:FSKRating;
        kmrbRating:KMRBRating;
        djctqRating:DJCTQRating;
        russiaRating:RussiaRating;
        rtcRating:RTCRating;
        ytRating:YTRating;
    }

    enum MPAARating{
        mpaaG,
        mpaaNc17,
        mpaaPg,
        mpaaPg13,
        mpaaR,
        mpaaUnrated
    }

    enum TVPGRating{
        tvpg14,
        tvpgG,
        tvpgMa,
        tvpgPg,
        tvpgUnrated,
        tvpgY,
        tvpgY7,
        tvpgY7Fv
    }


    enum BBFCRating{

        bbfc12,
        bbfc12a,
        bbfc15,
        bbfc18,
        bbfcPg ,
        bbfcR18 ,
        bbfcU ,
        bbfcUnrated
    }
    enum CHVRSRating{
        chvrs14a,
        chvrs18a,
        chvrsE,
        chvrsG,
        chvrsPg,
        chvrsR,
        chvrsUnrated}

    enum EIRINRating{
        eirinG ,
        eirinPg12 ,
        eirinR15plus ,
        eirinR18plus ,
        eirinUnrated
    }
    enum CBCFCRating{
        cbfcA ,
        cbfcS ,
        cbfcU ,
        cbfcUA ,
        cbfcUnrated
    }
    enum FMOCRating{
        fmoc10 ,
        fmoc12 ,
        fmoc16 ,
        fmoc18 ,
        fmocE ,
        fmocU ,
        fmocUnrated
    }
    enum ICAARating{
        icaa12 ,
        icaa13 ,
        icaa16 ,
        icaa18 ,
        icaa7 ,
        icaaApta ,
        icaaUnrated,
        icaaX
    }
    enum ACBRating {
        acbE ,
        acbG ,
        acbM ,
        acbMa15plus ,
        acbPg ,
        acbR18plus ,
        acbUnrated
    }
    enum OFLCRating{
        oflcG ,
        oflcM ,
        oflcPg ,
        oflcR13 ,
        oflcR15 ,
        oflcR16 ,
        oflcR18 ,
        oflcUnrated
    }
    enum FSKRating {
        fsk0 ,
        fsk12 ,
        fsk16 ,
        fsk18 ,
        fsk6 ,
        fskUnrated}
    enum KMRBRating {
        kmrb12plus ,
        kmrb15plus ,
        kmrbAll ,
        kmrbR ,
        kmrbTeenr,
        kmrbUnrated

    }
    enum DJCTQRating {
        djctq10 ,
        djctq12 ,
        djctq14 ,
        djctq16 ,
        djctq18 ,
        djctqL ,
        djctqUnrated
    }
    enum RussiaRating {

        russia0 ,
        russia12 ,
        russia16 ,
        russia18 ,
        russia6 ,
        russiaUnrated
    }
    enum RTCRating {

        rtcA ,
        rtcAa ,
        rtcB ,
        rtcB15 ,
        rtcC ,
        rtcD ,
        rtcUnrated
    }
    enum YTRating {
        ytAgeRestricted
    }
}