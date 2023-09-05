import React, { Component, Fragment } from 'react';
import {
  Formik
} from 'formik';
import {
  Stack, Form,
  Button, ButtonGroup, Modal, TextContainer
} from '@shopify/polaris';
import Select from 'react-select';
import DatePicker from 'react-datepicker';
import moment from 'moment';

import OptionsHelper from '../../../helpers/OptionsHelper';


class Action extends Component {
  constructor(props) {
    super(props);

    this.state = {
      showImportConfirm: false
    };

    this.handleSearch = this.handleSearch.bind(this);
    this.handleImport = this.handleImport.bind(this);
    this.handleImportAll = this.handleImportAll.bind(this);
    this.toggleImportConfirm = this.toggleImportConfirm.bind(this);
    this.formikRef = React.createRef();
  }

  componentWillReceiveProps(props) {
    this.formikRef.current.setValues(this.paramsToFormValues(props.search));
  }

  async handleSearch(values) {
    const params = this.formValuesToParams(values);
    const {
      onSearch
    } = this.props;
    await onSearch(params);
  }

  async handleImport() {
    const {
      onImport
    } = this.props;
    await onImport();
  }

  toggleImportConfirm() {
    this.setState({
      showImportConfirm: !this.state.showImportConfirm
    });
  }

  async handleImportAll() {
    this.setState({
      showImportConfirm: false,
      isImporting: true
    });
    const {
      onImportAll
    } = this.props;
    await onImportAll();
    this.setState({
      isImporting: false
    });
  }

  formValuesToParams(values) {
    return {
      inactive: values.inactive,
      league_id: values.league ? values.league.id : 0,
      start_at: moment(values.start_at).format('YYYY-MM-DD'),
      end_at: moment(values.end_at).format('YYYY-MM-DD')
    };
  }

  paramsToFormValues(params) {
    return {
      inactive: params.inactive === 'true',
      league: OptionsHelper.getItem(params.league_id, 'id', this.props.leagues),
      start_at: moment(params.start_at).toDate(),
      end_at: moment(params.end_at).toDate()
    };
  }

  render() {
    const {
      search, leagues
    } = this.props;

    const {
      showImportConfirm, isImporting
    } = this.state;

    return (
      <Fragment>
        <Modal
          open={showImportConfirm}
          onClose={this.toggleImportConfirm}
          title="Pull data"
          primaryAction={{
            content: 'OK',
            onAction: this.handleImportAll
          }}
          secondaryActions={[
            {
              content: 'Cancel',
              onAction: this.toggleImportConfirm
            }
          ]}
        >
          <Modal.Section>
            <TextContainer>
              Are you sure you want to import all games from deportes?
            </TextContainer>
          </Modal.Section>
        </Modal>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues(search)}
          onSubmit={this.handleSearch}
          render={({
            values,
            setFieldValue,
            handleSubmit
          }) => (
            <Form onSubmit={handleSubmit}>
              <Stack>
                <Stack.Item>
                  <Button
                    destructive={values.inactive}
                    primary={!values.inactive}
                    onClick={async () => {
                      await setFieldValue('inactive', !values.inactive);
                      handleSubmit();
                    }}
                    icon={values.inactive ? 'cancelSmall' : 'checkmark'}
                  >
                    { values.inactive ? 'Inactive' : 'Active' }
                  </Button>
                </Stack.Item>
                <Stack.Item
                  fill
                >
                  <Select
                    placeholder="League"
                    classNamePrefix="react-select"
                    indicatorSeparator={null}
                    options={leagues}
                    getOptionValue={option => option.id}
                    getOptionLabel={option => option.name}
                    value={values.league}
                    onChange={async (value) => {
                      await setFieldValue('league', value);
                      await handleSubmit();
                    }}
                  />
                </Stack.Item>
                <Stack.Item>
                  <Stack>
                    <Stack.Item>
                      <DatePicker
                        selected={values.start_at}
                        selectsStart
                        startDate={values.start_at}
                        endDate={values.end_at}
                        // showTimeSelect
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('start_at', value);
                          await setFieldValue('end_at', value);
                          await handleSubmit();
                        }}
                      />
                    </Stack.Item>
                    <Stack.Item>
                      <DatePicker
                        // readOnly
                        selected={values.end_at}
                        selectsEnd
                        startDate={values.start_at}
                        endDate={values.end_at}
                        timeIntervals={5}
                        onChange={async (value) => {
                          await setFieldValue('end_at', value);
                          await handleSubmit();
                        }}
                      />
                    </Stack.Item>
                  </Stack>
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      submit
                      primary
                      icon="search"
                    >
                        Search
                    </Button>
                    <Button
                      onClick={this.handleImport}
                      icon="import"
                    >
                        Import
                    </Button>
                    <Button
                      loading={isImporting}
                      onClick={this.toggleImportConfirm}
                      icon="import"
                    >
                        Import All
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            </Form>
          )}
        />
      </Fragment>
    );
  }
}

Action.defaultProps = {
  isSearching: false,
  search: {},
  leagues: [],
  onSearch: () => {},
  onImport: () => {},
  onImportAll: () => {}
};

export default Action;
