import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Form, FormLayout, Stack,
  InlineError, Button, TextStyle
} from '@shopify/polaris';
import moment from 'moment-timezone';
import DatePicker from 'react-datepicker';
import Select from 'react-select';

import OptionsHelper from '../../../helpers/OptionsHelper';

class Add extends Component {
  constructor(props) {
    super(props);
    this.formikRef = null;
    this.handleAdd = this.handleAdd.bind(this);
  }

  async handleAdd(values, bags) {
    const {
      onAdd
    } = this.props;
    await onAdd(this.formValuesToParams(values), bags, this);
  }

  formValuesToParams(values) {
    var start_at = moment(values.start_at).format('YYYY-MM-DD HH:mm:ss');
    start_at = moment.tz(start_at, "America/New_York");
    return {
      start_at: moment.utc(start_at).format('YYYY-MM-DD HH:mm:00'),
      home_team_id: values.home_team.value,
      away_team_id: values.away_team.value,
      between_players: this.props.between_players
    };
  }

  paramsToFormValues(params) {
    const {
      teams
    } = this.props;
    var start_at = moment(moment.utc(params.start_at).toDate()).tz("America/New_York").format('YYYY-MM-DD HH:mm:ss');
    return {
      start_at: moment(start_at).toDate(),
      home_team: OptionsHelper.getOption(params.home_team_id, teams),
      away_team: OptionsHelper.getOption(params.away_team_id, teams)
    };
  }

  render() {
    const {
      teams, homeTeamFirst, between_players
    } = this.props;

    return (
      <Formik
        ref={this.formikRef}
        initialValues={this.paramsToFormValues({})}
        validationSchema={
          Yup.object().shape({
            home_team: Yup.mixed().required(`${between_players ? 'Player one' : 'Home team'} is required!`),
            away_team: Yup.mixed().required(`${between_players ? 'Player two' : 'Away team'} is required!`)
          })
        }
        onSubmit={this.handleAdd}
        render={({
          values,
          errors,
          status,
          touched,
          // handleBlur,
          // handleChange,
          setFieldValue,
          handleSubmit,
          isSubmitting
        }) => (
          <Form onSubmit={handleSubmit}>
            <FormLayout>
              {status && <InlineError message={status} />}
              {homeTeamFirst || between_players ? (
                <Stack>
                  <Stack.Item>
                    <TextStyle>Start at</TextStyle>
                    <div>
                      <DatePicker
                        selected={values.start_at}
                        showTimeSelect
                        dateFormat="MM/dd/YYYY hh:mm a"
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('start_at', value);
                        }}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>{between_players ? 'Player one' : 'Home team'}</TextStyle>
                    <Select
                      placeholder={between_players ? 'Player one' : 'Home team'}
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={teams}
                      value={values.home_team}
                      onChange={async (value) => {
                        await setFieldValue('home_team', value);
                      }}
                    />
                    {(touched.home_team && !!errors.home_team) && <InlineError message={errors.home_team} />}
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>{between_players ? 'Player two' : 'Away team'}</TextStyle>
                    <Select
                      placeholder={between_players ? 'Player two' : 'Away team'}
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={teams}
                      value={values.away_team}
                      onChange={async (value) => {
                        await setFieldValue('away_team', value);
                      }}
                    />
                    {(touched.away_team && !!errors.away_team) && <InlineError message={errors.away_team} />}
                  </Stack.Item>
                </Stack>
              ) : (
                <Stack>
                  <Stack.Item>
                    <TextStyle>Start at</TextStyle>
                    <div>
                      <DatePicker
                        selected={values.start_at}
                        showTimeSelect
                        dateFormat="MM/dd/YYYY hh:mm a"
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('start_at', value);
                        }}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>Away team</TextStyle>
                    <Select
                      placeholder="Away team"
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={teams}
                      value={values.away_team}
                      onChange={async (value) => {
                        await setFieldValue('away_team', value);
                      }}
                    />
                    {(touched.away_team && !!errors.away_team) && <InlineError message={errors.away_team} />}
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>Home team</TextStyle>
                    <Select
                      placeholder="Home team"
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={teams}
                      value={values.home_team}
                      onChange={async (value) => {
                        await setFieldValue('home_team', value);
                      }}
                    />
                    {(touched.home_team && !!errors.home_team) && <InlineError message={errors.home_team} />}
                  </Stack.Item>
                </Stack>
              )}
              <FormLayout.Group>
                <Button
                  loading={isSubmitting}
                  submit
                  primary
                  icon="circlePlus"
                >
                    Add
                </Button>
              </FormLayout.Group>
            </FormLayout>
          </Form>
        )}
      />
    );
  }
}

Add.defaultProps = {
  homeTeamFirst: false,
  teams: [],
  onAdd: () => {}
};

export default Add;
