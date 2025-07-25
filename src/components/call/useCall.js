import { ref } from 'vue';
import { useAuthStore } from '@stores/auth';
import { createEcho } from '@/services/echo';
import { nextTick } from 'vue';
import axios from '@services/axios';

export function useCall({ localVideo, remoteVideo, recipientId, conversationId, onClose }) {
    const peerConnection = new RTCPeerConnection({
        iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
    });

    let localStream = null;
    const auth = useAuthStore();
    const echo = createEcho(auth.token);
    const isCalling = ref(false); // Guard to prevent multiple calls

    const startCall = async () => {
        if (isCalling.value) {
            console.log('Call already in progress, ignoring startCall');
            return;
        }
        isCalling.value = true;
        console.log('Starting call to recipient:', recipientId);
        try {
            if (!localVideo.value || !remoteVideo.value) {
                console.error('Video refs are not available');
                throw new Error('Video elements are not available');
            }

            // Check for available devices
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            const audioDevices = devices.filter(device => device.kind === 'audioinput');
            if (!videoDevices.length || !audioDevices.length) {
                console.error('No camera or microphone found');
                throw new Error('No camera or microphone available');
            }

            // Attempt to access media devices
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                localVideo.value.srcObject = localStream;
            } catch (err) {
                if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                    console.error('Camera or microphone is in use by another application');
                    throw new Error('Camera or microphone is currently in use. Please close other applications using these devices.');
                } else if (err.name === 'NotAllowedError') {
                    console.error('Permission denied for camera/microphone');
                    throw new Error('Permission denied for camera or microphone. Please allow access in your browser settings.');
                } else {
                    console.error('Error accessing media devices:', err);
                    throw new Error(`Failed to access media devices: ${err.message}`);
                }
            }

            // Check peerConnection state before adding tracks
            // if (peerConnection.signalingState === 'closed') {
            //     console.error('PeerConnection is closed, creating new instance');
            //     peerConnection = new RTCPeerConnection({
            //         iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
            //     });
            // }

            localStream.getTracks().forEach((track) => {
                peerConnection.addTrack(track, localStream);
            });

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);

            // Fetch CSRF token for Sanctum
            // await axios.get('/sanctum/csrf-cookie');

            // Send offer to backend
            await axios.post('call/initiate', {
                to: recipientId,
                offer,
                conversation_id: conversationId,
            });
            console.log('Call initiated, waiting for answer...');
            // Echo listeners
            echo.private(`chat.${conversationId}`)
                .listen('.CallAnswered', async (e) => {
                    console.log('Channel event received:', e);
                    await peerConnection.setRemoteDescription(new RTCSessionDescription(e.answer));
                })
                .listen('.ICECandidateReceived', async (e) => {
                    if (e.candidate) {
                        await peerConnection.addIceCandidate(new RTCIceCandidate(e.candidate));
                    }
                });

            peerConnection.onicecandidate = (e) => {
              console.log('ICE candidate:', e);
                if (e.candidate) {
                    axios.post('call/ice-candidate', {
                        to: recipientId,
                        candidate: e.candidate,
                        conversation_id: conversationId,
                    });
                }
            };

            peerConnection.ontrack = (e) => {
                remoteVideo.value.srcObject = e.streams[0];
            };
        } catch (error) {
            console.error('Error starting call:', error);
            isCalling.value = false;
            throw error;
        }
    };

    const endCall = () => {
        if (localStream) {
            localStream.getTracks().forEach((track) => track.stop());
            localStream = null;
        }
        peerConnection.close();
        echo.leave(`chat.${conversationId}`);
        isCalling.value = false;
        onClose();
    };

    return {
        startCall,
        endCall,
    };
}