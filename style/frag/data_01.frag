#ifdef GL_ES
precision mediump float;
#endif

uniform vec2 u_resolution;
uniform vec2 u_mouse;
uniform float u_time;

#define HASHSCALE1 0.1031
#define SEED 0.0

float random(in float p)
{
	p += SEED;
	vec3 p3  = fract(vec3(p) * HASHSCALE1);
    p3 += dot(p3, p3.yzx + 19.19);
    return fract((p3.x + p3.y) * p3.z);
}

float random(in vec2 p)
{
	p += SEED;
	vec3 p3  = fract(vec3(p.xyx) * HASHSCALE1);
    p3 += dot(p3, p3.yzx + 19.19);
    return fract((p3.x + p3.y) * p3.z);
}

void main()
{
	vec2 st = gl_FragCoord.xy/u_resolution.xy;

	vec2 grid = vec2(50.,3.);
	st *= grid;
	st.x = floor(st.x+ u_time * 5. * (2. + random(floor(st.y))));

	float fpos = fract(st.y);
    st.y = floor(st.y);

	vec3 color = vec3(step(random(floor(st.y + 1000.))*0.5+0.25, random(st)));

	color *= step(0.3,fpos);
	color += 0.047;

	color = min(color, 0.2*vec3(1, 0.78, 0.56));

	gl_FragColor = vec4(color,1.0);
}
